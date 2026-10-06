<?php
namespace App\Http\Controllers;
use App\Models\{ActivityLog, RawMaterial, MaterialReceipt, Notification, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $q = RawMaterial::query();
        if ($request->category) $q->where('category',$request->category);
        if ($request->search) $q->where('name','like',"%{$request->search}%")->orWhere('code','like',"%{$request->search}%");
        if ($request->low_stock) $q->whereRaw('current_stock < min_stock');
        $materials = $q->orderBy('name')->paginate(20);
        $lowStockCount = RawMaterial::whereRaw('current_stock < min_stock')->count();
        return view('bahan-baku.index', compact('materials','lowStockCount'));
    }

    public function create() { return view('bahan-baku.create'); }

    public function store(Request $request)
    {
        $request->validate(['code'=>'required|unique:raw_materials,code','name'=>'required','unit'=>'required','category'=>'required','min_stock'=>'required|numeric|min:0']);
        RawMaterial::create($request->all());
        return redirect()->route('bahan-baku.index')->with('success','Bahan baku berhasil ditambahkan.');
    }

    public function show(RawMaterial $rawMaterial)
    {
        $receipts = MaterialReceipt::where('raw_material_id',$rawMaterial->id)->with('confirmedBy')->latest()->get();
        $allocations = $rawMaterial->allocations()->with('order.product')->latest()->get();
        return view('bahan-baku.show', compact('rawMaterial','receipts','allocations'));
    }

    public function edit(RawMaterial $rawMaterial) { return view('bahan-baku.edit', compact('rawMaterial')); }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate(['name'=>'required','unit'=>'required','min_stock'=>'required|numeric|min:0']);
        $rawMaterial->update($request->all());
        return redirect()->route('bahan-baku.show',$rawMaterial)->with('success','Bahan baku berhasil diperbarui.');
    }

    public function receiptForm(RawMaterial $rawMaterial) { return view('bahan-baku.receipt', compact('rawMaterial')); }

    public function storeReceipt(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate(['qty'=>'required|numeric|min:0.01','unit_price'=>'required|numeric|min:0','receipt_date'=>'required|date','supplier'=>'nullable|string']);
        $total = $request->qty * $request->unit_price;
        MaterialReceipt::create(array_merge($request->only(['qty','unit_price','supplier','po_no','receipt_date','notes']),['raw_material_id'=>$rawMaterial->id,'total_price'=>$total,'confirmed_by'=>auth()->id()]));
        $rawMaterial->increment('current_stock', $request->qty);
        return redirect()->route('bahan-baku.show',$rawMaterial)->with('success',"Penerimaan {$request->qty} {$rawMaterial->unit} berhasil dicatat. Stok diperbarui.");
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return redirect()->route('bahan-baku.index')->with('success','Bahan baku berhasil dihapus.');
    }

    public function clearAll(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $request->validate(
            ['confirmation' => ['required', 'in:HAPUS SEMUA']],
            [
                'confirmation.required' => 'Ketik HAPUS SEMUA untuk melanjutkan.',
                'confirmation.in' => 'Teks konfirmasi tidak sesuai. Ketik HAPUS SEMUA.',
            ],
        );

        $deletedCount = DB::transaction(function (): int {
            $count = RawMaterial::query()->count();

            RawMaterial::query()->delete();

            ActivityLog::log(
                'raw_materials.clear_all',
                "Menghapus seluruh {$count} bahan baku beserta data terkait.",
                RawMaterial::class,
            );

            return $count;
        });

        return redirect()
            ->route('bahan-baku.index')
            ->with('success', "{$deletedCount} bahan baku beserta data terkait berhasil dihapus.");
    }
}
