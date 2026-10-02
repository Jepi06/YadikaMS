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
            'is_kelompok' => ['nullable', 'boolean'],
        ]);

        Tugas::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_lampiran' => $request->hasFile('file')
                ? $request->file('file')->store('tugas', 'public')
                : null,
            'batas_waktu' => $data['batas_waktu'],
            'is_kelompok' => $request->boolean('is_kelompok'),
        ]);

        return back()->with('status', 'Tugas berhasil dibuat.');
    }
    /** Edit judul/deskripsi/deadline/pengaturan buka-tutup. */
    public function update(Request $request, Tugas $tugas)
    {
        $this->authorizePengampu($tugas->pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'batas_waktu' => ['required', 'date'],
            'mode_buka' => ['required', 'in:bebas,manual,tanggal'],
            'mulai_pada' => ['nullable', 'date', 'required_if:mode_buka,tanggal'],
        ]);

        $tugas->update([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'batas_waktu' => $data['batas_waktu'],
            'mode_buka' => $data['mode_buka'],
            'mulai_pada' => $data['mode_buka'] === 'tanggal' ? $data['mulai_pada'] : null,
        ]);

        return back()->with('status', 'Tugas diperbarui.');
    }

    /** Buka/kunci manual (khusus mode_buka = manual). */
    public function toggleBuka(Tugas $tugas)
    {
        $this->authorizePengampu($tugas->pengampuMapel);
        abort_unless($tugas->mode_buka === 'manual', 422, 'Tugas ini bukan mode buka manual.');

        $tugas->update(['dibuka_manual' => ! $tugas->dibuka_manual]);

        return back()->with('status', $tugas->dibuka_manual ? 'Tugas dibuka.' : 'Tugas dikunci lagi.');
    }

    /** Tutup paksa / buka lagi, independen dari batas_waktu. */
    public function toggleTutup(Tugas $tugas)
    {
        $this->authorizePengampu($tugas->pengampuMapel);

        $tugas->update(['ditutup_manual' => ! $tugas->ditutup_manual]);

        return back()->with('status', $tugas->ditutup_manual ? 'Tugas ditutup paksa.' : 'Penutupan paksa dibatalkan.');
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

        // Kalau ini tugas kelompok, samain nilai ke SEMUA anggota kelompoknya.
        if ($pengumpulan->tugas_kelompok_id) {
            PengumpulanTugas::where('tugas_kelompok_id', $pengumpulan->tugas_kelompok_id)
                ->where('id', '!=', $pengumpulan->id)
                ->update([
                    'nilai' => $data['nilai'],
                    'catatan_guru' => $data['catatan_guru'] ?? null,
                    'dinilai_at' => now(),
                ]);
        }

        return back()->with('status', 'Nilai tersimpan untuk ' . $pengumpulan->siswa->nama . ($pengumpulan->tugas_kelompok_id ? ' (dan sekelompoknya).' : '.'));
    }
}
