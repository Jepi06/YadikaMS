<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Panel Super Admin nggak terikat ke satu guard tertentu — siapa pun
 * yang login (lewat PKL, SPMB, atau LMS) dan akunnya is_super_admin
 * boleh masuk. Middleware ini nyari user yang login di guard manapun,
 * lalu simpan ke request supaya controller/view nggak perlu nyari
 * ulang guard mana yang aktif.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::resolveAnyGuardUser();

        if (! $user) {
            return redirect('/')->withErrors(['email' => 'Silakan login terlebih dahulu (lewat PKL, SPMB, atau LMS).']);
        }

        if (! $user->isSuperAdmin()) {
            abort(403, 'Halaman ini khusus Super Admin.');
        }

        $request->attributes->set('superAdminUser', $user);

        return $next($request);
    }
}
