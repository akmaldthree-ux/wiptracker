<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('materialReceipts')->orderBy('name')->paginate(15);
        return view('master.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('master.supplier.form', ['supplier' => new Supplier, 'action' => 'create']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:suppliers,code',
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Supplier::create($data);
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('master.supplier.form', compact('supplier') + ['action' => 'edit']);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $supplier->update($data);
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->materialReceipts()->exists()) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat penerimaan bahan baku.');
        }
        $supplier->delete();
        return redirect()->route('master.supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
