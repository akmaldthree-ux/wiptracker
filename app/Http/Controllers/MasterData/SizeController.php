<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index() { return view('master.ukuran.index', ['sizes' => Size::orderBy('type')->orderBy('sort_order')->paginate(20)]); }
    public function create() { return view('master.ukuran.form', ['size' => new Size()]); }
    public function store(Request $request) {
        $request->validate(['name'=>'required','type'=>'required|in:letter,number']);
        Size::create($request->all());
        return redirect()->route('master.ukuran.index')->with('success','Ukuran berhasil ditambahkan.');
    }
    public function edit(Size $ukuran) { return view('master.ukuran.form', ['size' => $ukuran]); }
    public function update(Request $request, Size $ukuran) {
        $request->validate(['name'=>'required']);
        $ukuran->update($request->all());
        return redirect()->route('master.ukuran.index')->with('success','Ukuran berhasil diperbarui.');
    }
    public function destroy(Size $ukuran) { $ukuran->update(['is_active'=>false]); return back()->with('success','Ukuran dinonaktifkan.'); }
}
