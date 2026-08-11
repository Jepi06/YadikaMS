<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    private function authorizeKelas(PengampuMapel $pengampuMapel)
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');
        abort_unless($pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Mata pelajaran ini bukan untuk kelas Anda.');

        return $siswa;
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizeKelas($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'guru');
        $materi = $pengampuMapel->materi()->orderBy('urutan')->orderBy('created_at')->get();

        return view('lms.siswa.materi', compact('pengampuMapel', 'materi'));
    }
}
