<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardPublicController extends Controller
{
    /**
     * Landing page publik LMS. Kalau user sudah login di guard 'lms',
     * langsung arahkan ke dashboard sesuai rolenya.
     */
    public function index()
    {
        $user = Auth::guard('lms')->user();

        if ($user) {
            return $this->redirectToDashboard($user);
        }

        return view('lms.welcome');
    }

    public static function redirectToDashboard($user)
    {
        if ($user->isAdminLms()) {
            return redirect()->route('lms.admin.dashboard');
        }

        if ($user->isGuruLms()) {
            return redirect()->route('lms.guru.dashboard');
        }

        if ($user->isSiswaLms()) {
            return redirect()->route('lms.siswa.dashboard');
        }

        // Punya akses modul tapi tidak punya role dikenali → kembali ke landing
        return redirect()->route('lms')
            ->withErrors(['email' => 'Role akun Anda belum dikonfigurasi untuk LMS.']);
    }
}
