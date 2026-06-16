<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() { return view('master.produk.index', ['products' => Product::withCount('skus')->latest()->paginate(20)]); }
    public function create() { return view('master.produk.form', ['product' => new Product()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:products,code','name'=>'required']);
        Product::create($request->all());
        return redirect()->route('master.produk.index')->with('success','Produk berhasil ditambahkan.');
    }
    public function edit(Product $produk) { return view('master.produk.form', ['product' => $produk]); }
    public function update(Request $request, Product $produk) {
        $request->validate(['name'=>'required']);
        $produk->update($request->all());
        return redirect()->route('master.produk.index')->with('success','Produk berhasil diperbarui.');
    }
    public function destroy(Product $produk) { $produk->update(['is_active'=>false]); return back()->with('success','Produk dinonaktifkan.'); }
}
