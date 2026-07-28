<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengumpulanTugas;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('lms')->user();

        $kelasMengajar = $guru->pengampuMapel()
            ->with(['mataPelajaran', 'kelas.siswa'])
            ->latest()
            ->get();

        $tugasBelumDinilai = PengumpulanTugas::whereHas(
            'tugas.pengampuMapel',
            fn($q) => $q->where('guru_id', $guru->id)
        )->whereNull('dinilai_at')->count();

        return view('lms.guru.dashboard', compact('kelasMengajar', 'tugasBelumDinilai'));
    }
}
    