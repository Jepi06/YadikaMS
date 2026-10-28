<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\MateriSelesai;
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
        $siswa = $this->authorizeKelas($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'guru');

        $milik = $pengampuMapel->materi()->orderBy('urutan')->orderBy('created_at')->get();

        // Materi global dari kelas lain yang cakupannya mencakup kelas siswa ini
        // (satu angkatan, atau kelas pilihan guru). Ditaruh setelah materi kelas sendiri.
        $global = Materi::terlihatUntuk($pengampuMapel)
            ->where('pengampu_mapel_id', '!=', $pengampuMapel->id)
            ->with('pengampuMapel.kelas')
            ->orderBy('urutan')
            ->orderBy('created_at')
            ->get();

        $materi = $milik->concat($global)->values();

        $selesaiIds = MateriSelesai::where('siswa_id', $siswa->id)
            ->whereIn('materi_id', $materi->pluck('id'))
            ->pluck('materi_id')
            ->all();

        // null = terbuka, string = alasan terkunci
        $statusAkses = $materi->mapWithKeys(fn ($m) => [$m->id => $m->alasanTerkunci($siswa)]);

        return view('lms.siswa.materi', compact('pengampuMapel', 'materi', 'selesaiIds', 'statusAkses'));
    }

    public function tandaiSelesai(Materi $materi)
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');

        // Milik kelas siswa sendiri, atau materi global yang cakupannya mencakup kelas siswa
        abort_unless($materi->bisaDiaksesSiswa($siswa), 403, 'Materi ini bukan untuk kelas Anda.');

        // Gak boleh nandain selesai materi yang masih terkunci
        abort_if($materi->alasanTerkunci($siswa), 403, 'Materi ini masih terkunci.');

        MateriSelesai::firstOrCreate([
            'materi_id' => $materi->id,
            'siswa_id' => $siswa->id,
        ]);

        return back()->with('status', 'Materi ditandai selesai.');
    }
}