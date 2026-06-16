<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\{Series, Product};
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index() { return view('master.series.index', ['series' => Series::with('product','skus')->latest()->paginate(20)]); }
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
    public function destroy(Series $series) { $series->update(['is_active'=>false]); return back()->with('success','Series dinonaktifkan.'); }
}
