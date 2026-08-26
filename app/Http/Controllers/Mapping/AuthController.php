<?php

namespace App\Http\Controllers\Mapping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FORM LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('Mapping.Auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (
            !Auth::guard('pkl')->attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'is_active' => 1,
            ])
        ) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->onlyInput('email');
        }

        $user = Auth::guard('pkl')->user();

        // PERBAIKAN: sebelumnya di sini gak ada pengecekan sama sekali,
        // jadi SIAPA PUN yang is_active bisa login ke sistem PKL walau
        // gak punya role apa pun di modul ini. Sekarang disamain sama
        // pola LMS/SPMB — wajib hasPklAccess() (punya role di modul
        // 'pkl', ATAU super admin yang otomatis lolos lewat
        // User::hasModuleAccess()).
        if (!$user->hasPklAccess()) {
            Auth::guard('pkl')->logout();

            return back()->withErrors([
                'email' => 'Akun Anda tidak memiliki akses ke sistem PKL.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        Auth::shouldUse('pkl');

        return redirect()->route('pkl.dashboard')
            ->with('success', 'Login berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::guard('pkl')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('pkl.login')
            ->with('success', 'Berhasil logout');
    }
}
