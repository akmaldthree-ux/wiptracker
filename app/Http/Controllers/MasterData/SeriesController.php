<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\{Series, Product};
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request) {
        $q = Series::with('product','skus');
        if ($request->search) $q->where('name','like',"%{$request->search}%")->orWhere('code','like',"%{$request->search}%");
        if ($request->product_id) $q->where('product_id', $request->product_id);
        return view('master.series.index', [
            'series'   => $q->latest()->paginate(20)->withQueryString(),
            'products' => Product::where('is_active',true)->orderBy('name')->get(),
        ]);
    }
    public function create() { return view('master.series.form', ['series'=>new Series(), 'products'=>Product::where('is_active',true)->get()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:series,code','name'=>'required','product_id'=>'required|exists:products,id']);
        Series::create($request->all());
        return redirect()->route('master.series.index')->with('success','Series berhasil ditambahkan.');
    }
    public function edit(Series $series) { return view('master.series.form', ['series'=>$series, 'products'=>Product::where('is_active',true)->get()]); }
    public function update(Request $request, Series $series) {
        $request->validate(['name'=>'required','product_id'=>'required']);
        $series->update($request->all());
        return redirect()->route('master.series.index')->with('success','Series berhasil diperbarui.');
    }
    public function destroy(Series $series) { $series->delete(); return back()->with('success','Series berhasil dihapus.'); }
}
