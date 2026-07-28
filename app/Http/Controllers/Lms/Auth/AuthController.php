<?php

namespace App\Http\Controllers\Lms\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Lms\DashboardPublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('lms.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('lms')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $user = Auth::guard('lms')->user();

        if (!$user->hasLmsAccess()) {
            Auth::guard('lms')->logout();

            return back()->withErrors(['email' => 'Akun Anda tidak memiliki akses ke modul LMS.']);
        }

        $request->session()->regenerate();

        return DashboardPublicController::redirectToDashboard($user);
    }

    public function logout(Request $request)
    {
        Auth::guard('lms')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('lms');
    }
}
