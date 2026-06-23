<?php
namespace App\Http\Controllers;

use App\Models\{ProductionOrder, BomItem, MaterialRequirement, RawMaterial};
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductionOrder::with(['product','series','materialRequirements.rawMaterial'])
            ->whereIn('status', ['draft','active','on_hold']);

        if ($request->filter === 'shortage') {
            $q->where('materials_approved', false);
        } elseif ($request->filter === 'approved') {
            $q->where('materials_approved', true);
        }

        $orders = $q->orderBy('target_date')->get()->map(function ($order) {
            if (!$order->hasMaterialRequirements()) {
                $this->generateRequirements($order);
                $order->load('materialRequirements.rawMaterial');
            }
            $order->has_shortage = $order->hasMaterialShortage();
            return $order;
        });

        $totalOrders    = $orders->count();
        $readyOrders    = $orders->where('materials_approved', true)->count();
        $shortageOrders = $orders->where('has_shortage', true)->where('materials_approved', false)->count();
        $pendingOrders  = $orders->where('materials_approved', false)->where('has_shortage', false)->count();

        return view('procurement.index', compact('orders','totalOrders','readyOrders','shortageOrders','pendingOrders'));
    }

    public function show(ProductionOrder $order)
    {
        if (!$order->hasMaterialRequirements()) {
            $this->generateRequirements($order);
        }
        $order->load(['product','series','items.sku','materialRequirements.rawMaterial','materialsApprovedBy']);

        $requirements = $order->materialRequirements->map(function ($req) {
            $req->rawMaterial; // eager
            return $req;
        });

        $allSufficient = $requirements->every(fn($r) => $r->is_sufficient);

        return view('procurement.show', compact('order','requirements','allSufficient'));
    }

    public function regenerate(ProductionOrder $order)
    {
        abort_if(!in_array(auth()->user()->role, ['admin','procurement']), 403);
        $order->materialRequirements()->delete();
        $order->update(['materials_approved' => false, 'materials_approved_by' => null, 'materials_approved_at' => null]);
        $this->generateRequirements($order);
        return back()->with('success', 'Kebutuhan bahan berhasil dihitung ulang.');
    }

    public function approve(ProductionOrder $order)
    {
        abort_if(!in_array(auth()->user()->role, ['admin','procurement']), 403);

        $requirements = $order->materialRequirements()->with('rawMaterial')->get();
        foreach ($requirements as $req) {
            $req->rawMaterial->decrement('current_stock', $req->qty_needed);
        }

        $order->update([
            'materials_approved'    => true,
            'materials_approved_by' => auth()->id(),
            'materials_approved_at' => now(),
        ]);

        return back()->with('success', "Bahan baku untuk order {$order->order_no} disetujui dan stok telah dikurangi. Order siap dikirim ke Cutting.");
    }

    public function revoke(ProductionOrder $order)
    {
        abort_if(!in_array(auth()->user()->role, ['admin','procurement']), 403);

        $requirements = $order->materialRequirements()->with('rawMaterial')->get();
        foreach ($requirements as $req) {
            $req->rawMaterial->increment('current_stock', $req->qty_needed);
        }

        $order->update(['materials_approved' => false, 'materials_approved_by' => null, 'materials_approved_at' => null]);
        return back()->with('success', 'Persetujuan bahan dibatalkan dan stok dikembalikan.');
    }

    private function generateRequirements(ProductionOrder $order): void
    {
        $totalQty = $order->getTotalTargetQty();
        if ($totalQty <= 0) return;

        $bomItems = BomItem::where('product_id', $order->product_id)->with('rawMaterial')->get();
        foreach ($bomItems as $bom) {
            $qtyNeeded = $totalQty * $bom->qty_per_unit * (1 + $bom->waste_percentage / 100);
            MaterialRequirement::updateOrCreate(
                ['production_order_id' => $order->id, 'raw_material_id' => $bom->raw_material_id],
                ['qty_needed' => round($qtyNeeded, 4)]
            );
        }
    }
}
