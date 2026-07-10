<?php
namespace App\Http\Controllers;
use App\Models\{User, Station};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->isSupervisor(), 403);
        $users = User::with('station')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $stations = Station::where('is_active',true)->get();
        $roles = ['admin','supervisor','manager','pic_stasiun','procurement','staff_gudang','staff_produksi','ie','ppic'];
        return view('users.create', compact('stations','roles'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $request->validate(['name'=>'required','email'=>'required|email|unique:users','password'=>'required|min:8|confirmed','roles'=>'required|array|min:1']);
        $rolesArr = $request->roles;
        User::create(array_merge($request->only(['name','email','station_id','phone']),[
            'password'  => Hash::make($request->password),
            'is_active' => true,
            'roles'     => $rolesArr,
            'role'      => $rolesArr[0], // legacy compat
        ]));
        return redirect()->route('users.index')->with('success','User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        abort_if(!auth()->user()->isSupervisor() && auth()->id() !== $user->id, 403);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        abort_if(!auth()->user()->isAdmin() && auth()->id() !== $user->id, 403);
        $stations = Station::where('is_active',true)->get();
        $roles = ['admin','supervisor','manager','pic_stasiun','procurement','staff_gudang','staff_produksi','ie','ppic'];
        return view('users.edit', compact('user','stations','roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(!auth()->user()->isAdmin() && auth()->id() !== $user->id, 403);
        $request->validate(['name'=>'required','email'=>"required|email|unique:users,email,{$user->id}"]);
        $data = $request->only(['name','email','phone','station_id']);
        if (auth()->user()->isAdmin()) {
            $rolesArr = $request->roles ?? [];
            $data['roles']     = $rolesArr;
            $data['role']      = $rolesArr[0] ?? null; // legacy compat
            $data['is_active'] = $request->boolean('is_active');
        }
        if ($request->filled('password')) { $request->validate(['password'=>'min:8|confirmed']); $data['password'] = Hash::make($request->password); }
        $user->update($data);
        return redirect()->route('users.show',$user)->with('success','User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        abort_if($user->id === auth()->id(), 403, 'Tidak dapat menghapus akun sendiri.');
        $user->update(['is_active'=>false]);
        return redirect()->route('users.index')->with('success','User dinonaktifkan.');
    }

    public function profile() { return view('users.profile', ['user'=>auth()->user()]); }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate(['name'=>'required','phone'=>'nullable']);
        $data = $request->only(['name','phone']);
        if ($request->filled('password')) { $request->validate(['password'=>'min:8|confirmed']); $data['password'] = Hash::make($request->password); }
        $user->update($data);
        return back()->with('success','Profil berhasil diperbarui.');
    }
}
