<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PengumpulanTugas;
use App\Models\Lms\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    private function siswaAtauAbort()
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');

        return $siswa;
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $siswa = $this->siswaAtauAbort();
        abort_unless($pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Mata pelajaran ini bukan untuk kelas Anda.');

        $pengampuMapel->load('mataPelajaran');

        $tugas = $pengampuMapel->tugas()
            ->with(['pengumpulan' => fn($q) => $q->where('siswa_id', $siswa->id)])
            ->latest('batas_waktu')
            ->get();

        return view('lms.siswa.tugas', compact('pengampuMapel', 'tugas'));
    }

    public function show(Tugas $tugas)
    {
        $siswa = $this->siswaAtauAbort();
        $tugas->load('pengampuMapel.mataPelajaran');
        abort_unless($tugas->pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Tugas ini bukan untuk kelas Anda.');

        $pengumpulanSaya = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        return view('lms.siswa.tugas-detail', compact('tugas', 'pengumpulanSaya'));
    }

    public function kumpul(Request $request, Tugas $tugas)
    {
        $siswa = $this->siswaAtauAbort();
        $tugas->load('pengampuMapel');
        abort_unless($tugas->pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Tugas ini bukan untuk kelas Anda.');

        $data = $request->validate([
            'catatan_siswa' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'siswa_id' => $siswa->id],
            [
                'file_jawaban' => $request->file('file')->store('jawaban', 'public'),
                'catatan_siswa' => $data['catatan_siswa'] ?? null,
                'dikumpulkan_at' => now(),
                // reset penilaian lama kalau siswa kumpul ulang sebelum dinilai
                'nilai' => null,
                'catatan_guru' => null,
                'dinilai_at' => null,
            ]
        );

        return redirect()
            ->route('lms.siswa.tugas.show', $tugas)
            ->with('status', 'Tugas berhasil dikumpulkan.');
    }
}
