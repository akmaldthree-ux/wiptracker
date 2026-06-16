<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index() { return view('master.warna.index', ['colors' => Color::latest()->paginate(20)]); }
    public function create() { return view('master.warna.form', ['color' => new Color()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:colors,code','name'=>'required']);
        Color::create($request->all());
        return redirect()->route('master.warna.index')->with('success','Warna berhasil ditambahkan.');
    }
    public function edit(Color $warna) { return view('master.warna.form', ['color' => $warna]); }
    public function update(Request $request, Color $warna) {
        $request->validate(['name'=>'required']);
        $warna->update($request->all());
        return redirect()->route('master.warna.index')->with('success','Warna berhasil diperbarui.');
    }
    public function destroy(Color $warna) { $warna->update(['is_active'=>false]); return back()->with('success','Warna dinonaktifkan.'); }
}
