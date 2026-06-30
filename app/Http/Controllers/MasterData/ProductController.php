<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        $q = Product::withCount('skus');
        if ($request->search) $q->where('name','like',"%{$request->search}%")->orWhere('code','like',"%{$request->search}%");
        if ($request->category) $q->where('category', $request->category);
        return view('master.produk.index', ['products' => $q->latest()->paginate(20)->withQueryString()]);
    }
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
    public function destroy(Product $produk) { $produk->delete(); return back()->with('success','Produk berhasil dihapus.'); }
}
