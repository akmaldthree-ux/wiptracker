<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    public function index(Request $request)
    {
        $skus = Sku::query()
            ->with(['product', 'series', 'color', 'size', 'bomItems.rawMaterial'])
            ->where('is_active', true)
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->integer('product_id')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('sku_code', 'like', "%{$search}%")
                        ->orWhereHas('series', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('color', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('size', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('sku_code')
            ->paginate(15)
            ->withQueryString();

        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $allSkus = Sku::query()
            ->with(['product', 'series', 'color', 'size'])
            ->where('is_active', true)
            ->orderBy('sku_code')
            ->get();
        $materials = RawMaterial::query()->where('is_active', true)->orderBy('name')->get();

        return view('bom.index', compact('skus', 'products', 'allSkus', 'materials'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isSupervisor(), 403);

        $validated = $request->validate([
            'sku_id' => ['required', 'exists:skus,id'],
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty_per_unit' => ['required', 'numeric', 'min:0.0001'],
            'waste_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $sku = Sku::query()->findOrFail($validated['sku_id']);

        BomItem::updateOrCreate(
            ['sku_id' => $sku->id, 'raw_material_id' => $validated['raw_material_id']],
            [
                'product_id' => $sku->product_id,
                'qty_per_unit' => $validated['qty_per_unit'],
                'waste_percentage' => $validated['waste_percentage'],
                'notes' => $validated['notes'] ?? null,
            ],
        );

        return back()->with('success', "BOM {$sku->sku_code} berhasil disimpan.");
    }

    public function update(Request $request, BomItem $bom)
    {
        abort_unless($request->user()->isSupervisor(), 403);

        $validated = $request->validate([
            'qty_per_unit' => ['required', 'numeric', 'min:0.0001'],
            'waste_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);
        $bom->update($validated);

        return back()->with('success', 'BOM item berhasil diperbarui.');
    }

    public function destroy(Request $request, BomItem $bom)
    {
        abort_unless($request->user()->isSupervisor(), 403);

        $bom->delete();

        return back()->with('success', 'BOM item berhasil dihapus.');
    }

    public function copy(Request $request)
    {
        abort_unless($request->user()->isSupervisor(), 403);

        $validated = $request->validate([
            'source_sku_id' => ['required', 'exists:skus,id'],
            'target_sku_ids' => ['required', 'array', 'min:1'],
            'target_sku_ids.*' => ['required', 'distinct', 'exists:skus,id', 'different:source_sku_id'],
        ]);

        $source = Sku::query()->with('bomItems')->findOrFail($validated['source_sku_id']);

        if ($source->bomItems->isEmpty()) {
            return back()->with('error', "SKU {$source->sku_code} belum memiliki BOM untuk disalin.");
        }

        DB::transaction(function () use ($source, $validated): void {
            foreach (Sku::query()->whereKey($validated['target_sku_ids'])->get() as $target) {
                foreach ($source->bomItems as $item) {
                    BomItem::updateOrCreate(
                        ['sku_id' => $target->id, 'raw_material_id' => $item->raw_material_id],
                        [
                            'product_id' => $target->product_id,
                            'qty_per_unit' => $item->qty_per_unit,
                            'waste_percentage' => $item->waste_percentage,
                            'notes' => $item->notes,
                        ],
                    );
                }
            }
        });

        $targetCount = count($validated['target_sku_ids']);

        return back()->with('success', "BOM {$source->sku_code} berhasil disalin ke {$targetCount} SKU.");
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'sku_id' => ['required', 'exists:skus,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);
        $boms = BomItem::query()
            ->with('rawMaterial')
            ->where('sku_id', $validated['sku_id'])
            ->get();

        return response()->json($boms->map(function (BomItem $bom) use ($validated): array {
            $needed = $bom->getQtyNeeded((int) $validated['qty']);

            return [
                'material' => $bom->rawMaterial->name,
                'unit' => $bom->rawMaterial->unit,
                'qty_needed' => round($needed, 4),
                'stock' => $bom->rawMaterial->current_stock,
                'sufficient' => $bom->rawMaterial->current_stock >= $needed,
                'shortage' => max(0, $needed - $bom->rawMaterial->current_stock),
            ];
        }));
    }
}
