<?php

namespace App\Http\Controllers\Spmb\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('spmb.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('spmb')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $user = Auth::guard('spmb')->user();

        // Hanya admin SPMB yang boleh masuk ke panel ini
        if (!$user->hasSpmbAccess()) {
            Auth::guard('spmb')->logout();
            return back()->withErrors(['email' => 'Akun Anda tidak memiliki akses ke sistem SPMB.']);
        }

        $request->session()->regenerate();

        return redirect()->route('spmb.admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('spmb')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('spmb.login');
    }
}
