<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guard 'lms'. Kalau belum login, lempar ke halaman login LMS.
 * Kalau akun tidak punya akses modul LMS (is_active = false / tidak
 * punya role di modul lms), paksa logout + tolak akses.
 */
class AuthenticateLms
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('lms')->user();

        if (!$user) {
            return redirect()->route('lms.login');
        }

        if (!$user->hasLmsAccess()) {
            Auth::guard('lms')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('lms.login')
                ->withErrors(['email' => 'Akun Anda tidak memiliki akses ke modul LMS.']);
        }

        return $next($request);
    }
}
