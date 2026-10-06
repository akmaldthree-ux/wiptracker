<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequirement;
use App\Models\ProductionOrder;
use App\Models\RawMaterial;
use App\Services\BomCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function __construct(private readonly BomCalculator $bomCalculator) {}

    public function index(Request $request)
    {
        $query = ProductionOrder::with(['product', 'series', 'materialRequirements.rawMaterial'])
            ->whereIn('status', ['draft', 'active', 'on_hold']);

        if ($request->filter === 'shortage') {
            $query->where('materials_approved', false);
        } elseif ($request->filter === 'approved') {
            $query->where('materials_approved', true);
        }

        $orders = $query->orderBy('target_date')->get()->map(function (ProductionOrder $order): ProductionOrder {
            if (! $order->hasMaterialRequirements()) {
                $this->generateRequirements($order);
                $order->load('materialRequirements.rawMaterial');
            }
            $order->has_shortage = $order->hasMaterialShortage();

            return $order;
        });

        $totalOrders = $orders->count();
        $readyOrders = $orders->where('materials_approved', true)->count();
        $shortageOrders = $orders->where('has_shortage', true)->where('materials_approved', false)->count();
        $pendingOrders = $orders->where('materials_approved', false)->where('has_shortage', false)->count();

        return view('procurement.index', compact('orders', 'totalOrders', 'readyOrders', 'shortageOrders', 'pendingOrders'));
    }

    public function show(ProductionOrder $order)
    {
        if (! $order->hasMaterialRequirements()) {
            $this->generateRequirements($order);
        }

        $order->load(['product', 'series', 'items.sku', 'materialRequirements.rawMaterial', 'materialsApprovedBy']);
        $requirements = $order->materialRequirements;
        $missingBomSkus = $this->bomCalculator->forOrder($order)['missing_skus'];
        $allSufficient = $missingBomSkus->isEmpty()
            && $requirements->isNotEmpty()
            && $requirements->every(fn (MaterialRequirement $requirement): bool => $requirement->is_sufficient);

        return view('procurement.show', compact('order', 'requirements', 'allSufficient', 'missingBomSkus'));
    }

    public function regenerate(Request $request, ProductionOrder $order)
    {
        abort_unless($request->user()->canAccessProcurement(), 403);

        if ($order->materials_approved) {
            return back()->with('error', 'Batalkan persetujuan bahan sebelum melakukan hitung ulang.');
        }

        DB::transaction(function () use ($order): void {
            $order->materialRequirements()->delete();
            $order->update([
                'materials_approved' => false,
                'materials_approved_by' => null,
                'materials_approved_at' => null,
            ]);
            $this->generateRequirements($order);
        });

        $missing = $this->bomCalculator->forOrder($order->fresh())['missing_skus'];

        if ($missing->isNotEmpty()) {
            return back()->with('error', 'Perhitungan belum lengkap. SKU tanpa BOM: '.$missing->pluck('sku_code')->join(', '));
        }

        return back()->with('success', 'Kebutuhan bahan berhasil dihitung ulang berdasarkan BOM setiap SKU.');
    }

    public function approve(Request $request, ProductionOrder $order)
    {
        abort_unless($request->user()->canAccessProcurement(), 403);

        if ($order->materials_approved) {
            return back()->with('error', 'Bahan untuk order ini sudah disetujui.');
        }

        $calculation = $this->bomCalculator->forOrder($order);

        if ($calculation['missing_skus']->isNotEmpty()) {
            return back()->with(
                'error',
                'Persetujuan dibatalkan. Lengkapi BOM untuk SKU: '.$calculation['missing_skus']->pluck('sku_code')->join(', '),
            );
        }

        if ($calculation['requirements']->isEmpty()) {
            return back()->with('error', 'Persetujuan dibatalkan karena kebutuhan bahan masih kosong.');
        }

        DB::transaction(function () use ($order, $calculation, $request): void {
            $lockedOrder = ProductionOrder::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->materials_approved) {
                return;
            }

            $this->syncRequirements($lockedOrder, $calculation['requirements']);

            foreach ($lockedOrder->materialRequirements()->get() as $requirement) {
                RawMaterial::query()->lockForUpdate()->findOrFail($requirement->raw_material_id)
                    ->decrement('current_stock', $requirement->qty_needed);
            }

            $lockedOrder->update([
                'materials_approved' => true,
                'materials_approved_by' => $request->user()->id,
                'materials_approved_at' => now(),
            ]);
        });

        return back()->with('success', "Bahan baku untuk order {$order->order_no} disetujui dan stok telah dikurangi. Order siap dikirim ke Cutting.");
    }

    public function revoke(Request $request, ProductionOrder $order)
    {
        abort_unless($request->user()->canAccessProcurement(), 403);

        if (! $order->materials_approved) {
            return back()->with('error', 'Persetujuan bahan belum dilakukan.');
        }

        DB::transaction(function () use ($order): void {
            $lockedOrder = ProductionOrder::query()->lockForUpdate()->findOrFail($order->id);

            if (! $lockedOrder->materials_approved) {
                return;
            }

            foreach ($lockedOrder->materialRequirements()->get() as $requirement) {
                RawMaterial::query()->lockForUpdate()->findOrFail($requirement->raw_material_id)
                    ->increment('current_stock', $requirement->qty_needed);
            }

            $lockedOrder->update([
                'materials_approved' => false,
                'materials_approved_by' => null,
                'materials_approved_at' => null,
            ]);
        });

        return back()->with('success', 'Persetujuan bahan dibatalkan dan stok dikembalikan.');
    }

    private function generateRequirements(ProductionOrder $order): Collection
    {
        $calculation = $this->bomCalculator->forOrder($order);
        $this->syncRequirements($order, $calculation['requirements']);

        return $calculation['missing_skus'];
    }

    private function syncRequirements(ProductionOrder $order, Collection $requirements): void
    {
        $order->materialRequirements()->delete();

        foreach ($requirements as $requirement) {
            MaterialRequirement::create([
                'production_order_id' => $order->id,
                'raw_material_id' => $requirement['raw_material']->id,
                'qty_needed' => $requirement['qty_needed'],
            ]);
        }
    }
}
