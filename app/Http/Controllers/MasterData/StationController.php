<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index() { return view('master.stasiun.index', ['stations' => Station::orderBy('order_sequence')->paginate(20)]); }
    public function create() { return view('master.stasiun.form', ['station' => new Station()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:stations,code','name'=>'required','order_sequence'=>'required|integer']);
        Station::create(array_merge($request->all(), ['is_active' => $request->boolean('is_active'), 'is_final' => $request->boolean('is_final')]));
        return redirect()->route('master.stasiun.index')->with('success','Stasiun berhasil ditambahkan.');
    }
    public function edit(Station $stasiun) { return view('master.stasiun.form', ['station' => $stasiun]); }
    public function update(Request $request, Station $stasiun) {
        $request->validate(['name'=>'required','order_sequence'=>'required|integer']);
        $stasiun->update(array_merge($request->all(), ['is_active' => $request->boolean('is_active'), 'is_final' => $request->boolean('is_final')]));
        return redirect()->route('master.stasiun.index')->with('success','Stasiun berhasil diperbarui.');
    }
    public function destroy(Station $stasiun) { $stasiun->update(['is_active'=>false]); return back()->with('success','Stasiun dinonaktifkan.'); }
}
