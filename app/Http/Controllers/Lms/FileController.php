<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\PengumpulanTugas;
use App\Models\Lms\Tugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Serve file materi/tugas/jawaban LEWAT LARAVEL (bukan symlink public/storage).
 *
 * Alasan: di sebagian environment (mis. Laragon/Apache di Windows),
 * symlink `public/storage` diblok Apache (Options -FollowSymLinks)
 * sehingga akses langsung ke /storage/... berujung 403 dari web server,
 * bukan dari Laravel. Serve lewat route begini lebih portable, dan
 * sekalian bisa ditambah pengecekan otorisasi (bukan sekadar tebak URL).
 */
class FileController extends Controller
{
    public function materi(Materi $materi)
    {
        $this->authorizeAksesKelas($materi->pengampuMapel->guru_id, $materi->pengampuMapel->kelas_id);

        abort_unless($materi->file_path, 404);

        return Storage::disk('public')->response($materi->file_path);
    }

    public function tugasLampiran(Tugas $tugas)
    {
        $this->authorizeAksesKelas($tugas->pengampuMapel->guru_id, $tugas->pengampuMapel->kelas_id);

        abort_unless($tugas->file_lampiran, 404);

        return Storage::disk('public')->response($tugas->file_lampiran);
    }

    public function jawaban(PengumpulanTugas $pengumpulan)
    {
        $user = Auth::guard('lms')->user();
        $guruPengampuId = $pengumpulan->tugas->pengampuMapel->guru_id;
        $siswaPemilikId = $pengumpulan->siswa->user_id;

        abort_unless(
            $user->id === $guruPengampuId || $user->id === $siswaPemilikId,
            403,
            'Anda tidak berhak mengakses file ini.'
        );

        abort_unless($pengumpulan->file_jawaban, 404);

        return Storage::disk('public')->response($pengumpulan->file_jawaban);
    }

    /** Guru pengampu ATAU siswa di kelas yang sama boleh akses. */
    private function authorizeAksesKelas(int $guruId, int $kelasId): void
    {
        $user = Auth::guard('lms')->user();

        if ($user->id === $guruId) {
            return;
        }

        $siswa = $user->siswa;
        abort_unless($siswa && $siswa->kelas_id === $kelasId, 403, 'Anda tidak berhak mengakses file ini.');
    }
}
