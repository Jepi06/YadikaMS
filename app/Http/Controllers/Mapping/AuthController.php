<?php

namespace App\Http\Controllers\Mapping;

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
            Auth::guard('pkl')->attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'is_active' => 1,
            ])
        ) {
            $request->session()->regenerate();
            Auth::shouldUse('pkl');
            return redirect()->route('pkl.dashboard')
                ->with('success', 'Login berhasil');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
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
