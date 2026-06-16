<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|min:6'], [
            'email.required' => 'Email wajib diisi', 'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => true], $request->remember)) {
            auth()->user()->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
