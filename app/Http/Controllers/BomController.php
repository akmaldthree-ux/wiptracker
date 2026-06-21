<?php
namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index()
    {
        $products = Product::with(['bomItems.rawMaterial'])->where('is_active', true)->get();
        return view('bom.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'raw_material_id'  => 'required|exists:raw_materials,id',
            'qty_per_unit'     => 'required|numeric|min:0.0001',
            'waste_percentage' => 'required|numeric|min:0|max:100',
            'notes'            => 'nullable|string|max:255',
        ]);

        BomItem::updateOrCreate(
            ['product_id' => $request->product_id, 'raw_material_id' => $request->raw_material_id],
            ['qty_per_unit' => $request->qty_per_unit, 'waste_percentage' => $request->waste_percentage, 'notes' => $request->notes]
        );

        return back()->with('success', 'BOM item berhasil disimpan.');
    }

    public function destroy(BomItem $bom)
    {
        $bom->delete();
        return back()->with('success', 'BOM item berhasil dihapus.');
    }

    public function calculate(Request $request)
    {
        $request->validate(['product_id'=>'required|exists:products,id','qty'=>'required|integer|min:1']);
        $boms = BomItem::with('rawMaterial')->where('product_id', $request->product_id)->get();

        $result = $boms->map(fn($b) => [
            'material'    => $b->rawMaterial->name,
            'unit'        => $b->rawMaterial->unit,
            'qty_needed'  => round($b->getQtyNeeded($request->qty), 4),
            'stock'       => $b->rawMaterial->current_stock,
            'sufficient'  => $b->rawMaterial->current_stock >= $b->getQtyNeeded($request->qty),
            'shortage'    => max(0, $b->getQtyNeeded($request->qty) - $b->rawMaterial->current_stock),
        ]);

        return response()->json($result);
    }
}
