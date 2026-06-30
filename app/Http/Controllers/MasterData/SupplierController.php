<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request) {
        $q = Supplier::query();
        if ($request->search) $q->where('name','like',"%{$request->search}%")->orWhere('code','like',"%{$request->search}%");
        $suppliers = $q->orderBy('name')->paginate(15)->withQueryString();
        return view('master.supplier.index', compact('suppliers'));
    }

    public function create() {
        return view('master.supplier.form');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        Supplier::create([
            'name' => $request->name,
            'code' => $request->code,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier) {
        return view('master.supplier.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier) {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,'.$supplier->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $supplier->update([
            'name' => $request->name,
            'code' => $request->code,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier) {
        if ($supplier->materialReceipts()->count() > 0) {
            return redirect()->back()->with('error', 'Supplier tidak dapat dihapus karena memiliki data penerimaan bahan baku.');
        }
        $supplier->delete();
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
