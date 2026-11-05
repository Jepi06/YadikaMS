<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\NilaiPraktik;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\Praktik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PraktikController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    /** Halaman input nilai praktik: matriks siswa x kegiatan praktik. */
    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $daftarPraktik = Praktik::where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        // [siswa_id][praktik_id] => nilai
        $nilai = NilaiPraktik::whereIn('praktik_id', $daftarPraktik->pluck('id'))
            ->get()
            ->groupBy('siswa_id')
            ->map(fn ($rows) => $rows->pluck('nilai', 'praktik_id'));

        return view('lms.guru.praktik', [
            'pengampuMapel' => $pengampuMapel,
            'daftarPraktik' => $daftarPraktik,
            'siswaList' => $pengampuMapel->kelas->siswa,
            'nilai' => $nilai,
        ]);
    }

    /** Tambah 1 kegiatan praktik baru (bisa berkali-kali). */
    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);

        Praktik::create($data + ['pengampu_mapel_id' => $pengampuMapel->id]);

        return back()->with('status', 'Praktik "' . $data['judul'] . '" ditambahkan. Silakan isi nilai siswa.');
    }

    /** Simpan semua nilai (dan judul/tanggal praktik) sekaligus. */
    public function simpan(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $request->validate([
            'praktik' => ['nullable', 'array'],
            'praktik.*.judul' => ['required', 'string', 'max:255'],
            'praktik.*.tanggal' => ['nullable', 'date'],
            'nilai' => ['nullable', 'array'],
            'nilai.*.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $pengampuMapel->load('kelas.siswa');
        $siswaIds = $pengampuMapel->kelas->siswa->pluck('id')->all();
        $praktikMilik = Praktik::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('id');

        DB::transaction(function () use ($request, $siswaIds, $praktikMilik) {
            foreach ($request->input('praktik', []) as $praktikId => $meta) {
                $praktik = $praktikMilik->get($praktikId);
                if ($praktik) {
                    $praktik->update([
                        'judul' => $meta['judul'],
                        'tanggal' => $meta['tanggal'] ?: null,
                    ]);
                }
            }

            foreach ($request->input('nilai', []) as $siswaId => $perPraktik) {
                if (! in_array((int) $siswaId, $siswaIds, true)) {
                    continue;
                }
                foreach ($perPraktik as $praktikId => $angka) {
                    if (! $praktikMilik->has($praktikId)) {
                        continue;
                    }

                    if ($angka === null || $angka === '') {
                        // dikosongkan -> hapus (tidak dihitung ke rata-rata)
                        NilaiPraktik::where('praktik_id', $praktikId)->where('siswa_id', $siswaId)->delete();
                    } else {
                        NilaiPraktik::updateOrCreate(
                            ['praktik_id' => $praktikId, 'siswa_id' => $siswaId],
                            ['nilai' => $angka]
                        );
                    }
                }
            }
        });

        return back()->with('status', 'Nilai praktik tersimpan.');
    }

    /** Hapus 1 kegiatan praktik beserta nilainya. */
    public function destroy(PengampuMapel $pengampuMapel, Praktik $praktik)
    {
        $this->authorizePengampu($pengampuMapel);
        abort_unless($praktik->pengampu_mapel_id === $pengampuMapel->id, 404);

        $judul = $praktik->judul;
        $praktik->delete(); // nilai ikut terhapus (cascade)

        return back()->with('status', 'Praktik "' . $judul . '" dihapus.');
    }
}
