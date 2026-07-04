<?php

// FILE: app/Http/Middleware/AuthenticatePpdb.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePpdb
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('ppdb')->check()) {
            return redirect()->route('ppdb.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::guard('ppdb')->user();

        if (! $user->hasPpdbAccess()) {
            Auth::guard('ppdb')->logout();
            return redirect()->route('ppdb.login')
                ->with('error', 'Akun Anda tidak memiliki akses ke sistem PPDB.');
        }

        return $next($request);
    }
}
