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
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            (int) $pengampuMapel->guru_id === (int) Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    private function resolveTanggal(Request $request): string
    {
        $tanggal = $request->input('tanggal');

        if ($tanggal && Carbon::hasFormat($tanggal, 'Y-m-d')) {
            return $tanggal;
        }

        return now()->toDateString();
    }

    public function index(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load(['mataPelajaran', 'kelas.siswa']);

        $tanggal = $this->resolveTanggal($request);
        $isHariIni = $tanggal === now()->toDateString();

        $sesi = $isHariIni
            ? SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
                ->where('tanggal', $tanggal)
                ->first()
            : null;

        $presensiSiswa = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $scanUrl = $sesi
            ? route('lms.siswa.presensi.scan', ['token' => $sesi->tokenDinamis()])
            : null;

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

    /** Endpoint polling, dipanggil JS di halaman guru untuk ambil QR terbaru. */
    public function qr(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $sesi = SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->where('tanggal', now()->toDateString())
            ->first();

        if (! $sesi || ! $sesi->masih_aktif) {
            return response()->json(['aktif' => false]);
        }

        return response()
            ->json([
                'aktif' => true,
                'url'   => route('lms.siswa.presensi.scan', ['token' => $sesi->tokenDinamis()]),
                'sisa'  => SesiPresensi::sisaDetik(),
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function buka(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $sesi = SesiPresensi::firstOrNew([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'tanggal' => now()->toDateString(),
        ]);

        $sesi->created_by = Auth::guard('lms')->id();
        $sesi->dibuka_at = now();
        $sesi->ditutup_at = now()->addHour();
        $sesi->save();

        return back()->with('status', 'Sesi presensi dibuka sampai ' . $sesi->ditutup_at->format('H:i') . ' — tampilkan QR ke siswa.');
    }

    public function tutup(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->where('tanggal', now()->toDateString())
            ->update(['ditutup_at' => now()]);

        return back()->with('status', 'Sesi presensi ditutup.');
    }

    public function simpanManual(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:Hadir,Izin,Sakit,Alpa'],
        ]);

        foreach ($data['status'] as $siswaId => $status) {
            PresensiLms::updateOrCreate(
                [
                    'pengampu_mapel_id' => $pengampuMapel->id,
                    'siswa_id' => $siswaId,
                    'tanggal' => $data['tanggal'],
                ],
                [
                    'status' => $status,
                    'dicatat_manual_oleh' => Auth::guard('lms')->id(),
                ]
            );
        }

        return back()->with('status', 'Presensi manual berhasil disimpan.');
    }

    public function rekap(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $request->validate([
            'dari'   => ['nullable', 'date_format:Y-m-d'],
            'sampai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dari'],
        ]);

        $pengampuMapel->load(['mataPelajaran', 'kelas.siswa']);

        $presensi = PresensiLms::query()
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->when($request->filled('dari'), fn($q) => $q->whereDate('tanggal', '>=', $request->input('dari')))
            ->when($request->filled('sampai'), fn($q) => $q->whereDate('tanggal', '<=', $request->input('sampai')))
            ->get();

        $perSiswa = $presensi->groupBy('siswa_id');

        $hitung = fn($items, string $status) => $items
            ->filter(fn($x) => strtolower((string) $x->status) === $status)
            ->count();

        // Basis dari daftar siswa kelas, jadi siswa tanpa data tetap tampil (0%)
        $rekap = $pengampuMapel->kelas->siswa->map(function ($siswa) use ($perSiswa, $hitung) {
            $p = $perSiswa->get($siswa->id, collect());

            return [
                'siswa' => $siswa,
                'hadir' => $hitung($p, 'hadir'),
                'izin'  => $hitung($p, 'izin'),
                'sakit' => $hitung($p, 'sakit'),
                'alpa'  => $hitung($p, 'alpa'),
            ];
        });

        // Jumlah tanggal pertemuan unik dalam rentang yang dipilih
        $totalPertemuan = $presensi
            ->map(fn($x) => $x->tanggal->toDateString())
            ->unique()
            ->count();

        return view('lms.guru.presensi-rekap', compact('pengampuMapel', 'rekap', 'totalPertemuan'));
    }
}