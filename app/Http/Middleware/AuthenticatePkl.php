<?php

// FILE: app/Http/Middleware/AuthenticatePkl.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePkl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('pkl')->check()) {
            return redirect()->route('pkl.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::guard('pkl')->user();

        if (! $user->hasPklAccess()) {
            Auth::guard('pkl')->logout();
            return redirect()->route('pkl.login')
                ->with('error', 'Akun Anda tidak memiliki akses ke sistem PKL.');
        }

        return $next($request);
    }
}
