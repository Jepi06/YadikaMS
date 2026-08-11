<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PengumpulanTugas;
use App\Models\Lms\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    private function authorizeTugas(Tugas $tugas): void
    {
        abort_unless(
            $tugas->pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas');

        $tugas = $pengampuMapel->tugas()
            ->withCount('pengumpulan')
            ->latest('batas_waktu')
            ->get();

        return view('lms.guru.tugas', compact('pengampuMapel', 'tugas'));
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
            'batas_waktu' => ['required', 'date'],
        ]);

        Tugas::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_lampiran' => $request->hasFile('file')
                ? $request->file('file')->store('tugas', 'public')
                : null,
            'batas_waktu' => $data['batas_waktu'],
        ]);

        return back()->with('status', 'Tugas berhasil dibuat.');
    }

    public function destroy(Tugas $tugas)
    {
        $this->authorizeTugas($tugas);

        if ($tugas->file_lampiran) {
            Storage::disk('public')->delete($tugas->file_lampiran);
        }

        $tugas->delete();

        return back()->with('status', 'Tugas dihapus.');
    }

    /** Daftar pengumpulan siswa untuk 1 tugas + form nilai. */
    public function kumpulan(Tugas $tugas)
    {
        $this->authorizeTugas($tugas);

        $tugas->load('pengampuMapel.mataPelajaran', 'pengampuMapel.kelas.siswa');

        $pengumpulan = $tugas->pengumpulan()->with('siswa')->get()->keyBy('siswa_id');

        return view('lms.guru.tugas-kumpulan', compact('tugas', 'pengumpulan'));
    }

    public function simpanNilai(Request $request, PengumpulanTugas $pengumpulan)
    {
        $this->authorizeTugas($pengumpulan->tugas);

        $data = $request->validate([
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_guru' => ['nullable', 'string', 'max:500'],
        ]);

        $pengumpulan->update([
            'nilai' => $data['nilai'],
            'catatan_guru' => $data['catatan_guru'] ?? null,
            'dinilai_at' => now(),
        ]);

        return back()->with('status', 'Nilai tersimpan untuk ' . $pengumpulan->siswa->nama . '.');
    }
}
