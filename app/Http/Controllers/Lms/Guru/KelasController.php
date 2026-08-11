<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    public function index()
    {
        $kelasMengajar = Auth::guard('lms')->user()
            ->pengampuMapel()
            ->with(['mataPelajaran', 'kelas.siswa'])
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        return view('lms.guru.kelas', compact('kelasMengajar'));
    }
}
