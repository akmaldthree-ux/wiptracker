<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\SewingLocation;
use Illuminate\Http\Request;

class SewingLocationController extends Controller
{
    public function index()
    {
        $locations = SewingLocation::latest()->paginate(20);
        return view('master.sewing-location.index', compact('locations'));
    }

    public function create()
    {
        $location = new SewingLocation();
        return view('master.sewing-location.form', compact('location'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:sewing_locations,code',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        SewingLocation::create([
            'code'        => strtoupper($request->code),
            'name'        => $request->name,
            'address'     => $request->address,
            'description' => $request->description,
            'capacity'    => $request->capacity,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('master.sewing-location.index')->with('success', 'Tempat Sewing berhasil ditambahkan.');
    }

    public function edit(SewingLocation $sewingLocation)
    {
        $location = $sewingLocation;
        return view('master.sewing-location.form', compact('location'));
    }

    public function update(Request $request, SewingLocation $sewingLocation)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:sewing_locations,code,' . $sewingLocation->id,
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $sewingLocation->update([
            'code'        => strtoupper($request->code),
            'name'        => $request->name,
            'address'     => $request->address,
            'description' => $request->description,
            'capacity'    => $request->capacity,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('master.sewing-location.index')->with('success', 'Tempat Sewing berhasil diperbarui.');
    }

    public function destroy(SewingLocation $sewingLocation)
    {
        if ($sewingLocation->handovers()->exists()) {
            return back()->with('error', 'Tempat Sewing tidak dapat dihapus karena sudah digunakan pada data handover.');
        }

        $sewingLocation->delete();
        return redirect()->route('master.sewing-location.index')->with('success', 'Tempat Sewing berhasil dihapus.');
    }
}
