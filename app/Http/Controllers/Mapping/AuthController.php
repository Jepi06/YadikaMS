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
            ! Auth::guard('pkl')->attempt([
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

        if (! $user->hasPklAccess()) {
            Auth::guard('pkl')->logout();

            return back()->withErrors([
                'email' => 'Akun Anda tidak memiliki akses ke sistem PKL.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        Auth::shouldUse('pkl');

        // PERBAIKAN: super admin login dari sistem MANAPUN (PKL/SPMB/LMS)
        // selalu diarahkan ke Panel Super Admin, bukan ke dashboard
        // sistem tempat dia login — biar administrasi selalu ketemu di
        // satu tempat yang sama, konsisten di mana pun dia masuk.
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Login berhasil');
        }

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
    public function masukDariLms(Request $request)
    {
        $user = Auth::guard('lms')->user();

        if (! $user || ! $user->hasPklRole('wali_kelas', 'guru_bk', 'kesiswaan', 'kepala_jurusan')) {
            abort(403, 'Anda tidak memiliki akses approval PKL.');
        }

        // Kalau di guard pkl masih login sebagai akun lain, ganti ke akun ini
        $sedangLogin = Auth::guard('pkl')->user();
        if ($sedangLogin && $sedangLogin->id !== $user->id) {
            Auth::guard('pkl')->logout();
        }

        if (! Auth::guard('pkl')->check()) {
            Auth::guard('pkl')->login($user);
            $request->session()->regenerate();
        }

        return redirect()->route('approval.index');
    }
}
