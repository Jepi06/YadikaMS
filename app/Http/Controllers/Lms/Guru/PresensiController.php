<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    /** Pastikan guru yang login memang pengampu kelas ini. */
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    /**
     * Ambil tanggal dari query/input, fallback ke hari ini.
     * Divalidasi format supaya tidak sembarang string masuk ke query.
     */
    private function resolveTanggal(Request $request): string
    {
        $tanggal = $request->input('tanggal');

        if ($tanggal && Carbon::hasFormat($tanggal, 'Y-m-d')) {
            return $tanggal;
        }

        return now()->toDateString();
    }

    /** Halaman kelola presensi untuk 1 kelas mengajar, per tanggal (default hari ini). */
    public function index(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load(['mataPelajaran', 'kelas.siswa']);

        $tanggal = $this->resolveTanggal($request);
        $isHariIni = $tanggal === now()->toDateString();

        // Sesi barcode (QR) hanya relevan untuk hari ini — presensi tanggal
        // lampau/mendatang hanya bisa lewat input manual.
        $sesi = $isHariIni
            ? SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
                ->where('tanggal', $tanggal)
                ->first()
            : null;

        $presensiSiswa = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $scanUrl = $sesi ? route('lms.siswa.presensi.scan', ['token' => $sesi->token]) : null;

        $tidakHadir = $presensiSiswa->filter(fn($p) => $p->status !== 'Hadir');

        return view('lms.guru.presensi', compact(
            'pengampuMapel',
            'sesi',
            'presensiSiswa',
            'tanggal',
            'isHariIni',
            'scanUrl',
            'tidakHadir'
        ));
    }

    /** Buka (atau buka-ulang) sesi presensi barcode untuk hari ini. */
    public function buka(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $sesi = SesiPresensi::firstOrNew([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'tanggal' => now()->toDateString(),
        ]);

        $sesi->created_by = Auth::guard('lms')->id();
        $sesi->dibuka_at = now();
        $sesi->ditutup_at = null; // reset kalau sebelumnya sempat ditutup
        $sesi->save();

        return back()->with('status', 'Sesi presensi dibuka — tampilkan QR ke siswa.');
    }

    public function tutup(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->where('tanggal', now()->toDateString())
            ->update(['ditutup_at' => now()]);

        return back()->with('status', 'Sesi presensi ditutup.');
    }

    /** Simpan/koreksi presensi manual untuk seluruh siswa sekaligus, di tanggal yang dipilih. */
    public function simpanManual(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'presensi' => ['required', 'array'],
            'presensi.*.status' => ['required', 'in:Hadir,Izin,Sakit,Alpa'],
            'presensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['presensi'] as $siswaId => $row) {
            PresensiLms::updateOrCreate(
                [
                    'pengampu_mapel_id' => $pengampuMapel->id,
                    'siswa_id' => $siswaId,
                    'tanggal' => $data['tanggal'],
                ],
                [
                    'status' => $row['status'],
                    'keterangan' => $row['keterangan'] ?? null,
                    'sumber' => 'manual',
                ]
            );
        }

        return redirect()
            ->route('lms.guru.presensi.index', [$pengampuMapel, 'tanggal' => $data['tanggal']])
            ->with('status', 'Presensi tanggal ' . Carbon::parse($data['tanggal'])->translatedFormat('d M Y') . ' tersimpan.');
    }

    /**
     * Rekap presensi per siswa dalam rentang tanggal (default: 30 hari
     * terakhir) — total Hadir/Izin/Sakit/Alpa dan persentase kehadiran.
     */
    public function rekap(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load(['mataPelajaran', 'kelas.siswa']);

        $dari = $request->input('dari') ?: now()->subDays(30)->toDateString();
        $sampai = $request->input('sampai') ?: now()->toDateString();

        $presensi = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->get()
            ->groupBy('siswa_id');

        // Jumlah pertemuan = jumlah tanggal unik yang punya minimal 1 catatan presensi
        $totalPertemuan = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->distinct('tanggal')
            ->count('tanggal');

        $rekap = $pengampuMapel->kelas->siswa->map(function ($siswa) use ($presensi, $totalPertemuan) {
            $rows = $presensi->get($siswa->id, collect());

            $hadir = $rows->where('status', 'Hadir')->count();
            $izin = $rows->where('status', 'Izin')->count();
            $sakit = $rows->where('status', 'Sakit')->count();
            $alpa = $rows->where('status', 'Alpa')->count();

            return (object) [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => $totalPertemuan > 0 ? round($hadir / $totalPertemuan * 100) : 0,
            ];
        });

        return view('lms.guru.presensi-rekap', compact(
            'pengampuMapel',
            'rekap',
            'dari',
            'sampai',
            'totalPertemuan'
        ));
    }
}
