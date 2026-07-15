<?php

// FILE: app/Http/Middleware/AuthenticateSpmb.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSpmb
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('spmb')->check()) {
            return redirect()->route('spmb.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::guard('spmb')->user();

        if (!$user->hasSpmbAccess()) {
            Auth::guard('spmb')->logout();
            return redirect()->route('spmb.login')
                ->with('error', 'Akun Anda tidak memiliki akses ke sistem SPMB.');
        }

        return $next($request);
    }
}
