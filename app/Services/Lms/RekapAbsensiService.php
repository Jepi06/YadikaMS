<?php

namespace App\Services\Lms;

use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Support\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RekapAbsensiService
{
    /** Daftar periode (tahun ajaran + semester) yang ada di kelas ini. */
    public static function periodeList(Kelas $kelas)
    {
        return PengampuMapel::where('kelas_id', $kelas->id)
            ->select('tahun_ajaran', 'semester')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();
    }

    /**
     * Susun semua data rekap absensi 1 kelas.
     * Return array siap di-merge ke view (hariMasukPerBulan, rekap, dst).
     */
    public static function susun(Kelas $kelas, $tahunAjaran, $semester, ?string $bulanReq = null): array
    {
        $kelas->loadMissing('siswa');

        $pengampuIds = PengampuMapel::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->pluck('id');

        $presensi = DB::table('presensi_lms')
            ->whereIn('pengampu_mapel_id', $pengampuIds)
            ->get(['siswa_id', 'tanggal', 'status'])
            ->map(function ($p) {
                $p->tanggal = Carbon::parse($p->tanggal)->format('Y-m-d');
                return $p;
            });

        $hariMasukPerBulan = $presensi
            ->groupBy(fn($p) => substr($p->tanggal, 0, 7))
            ->map(fn($grup) => $grup->pluck('tanggal')->unique()->count())
            ->sortKeys();

        $totalHariMasuk = $presensi->pluck('tanggal')->unique()->count();
        $jumlahSiswa = $kelas->siswa->count();

        $persen = fn($n, $slot) => $slot > 0 ? round($n / $slot * 100, 1) : 0;

        // Rekap kelas per bulan
        $rekapKelasPerBulan = [];
        foreach ($hariMasukPerBulan as $bulan => $hariAktif) {
            $slot = $hariAktif * $jumlahSiswa;
            $bulanIni = $presensi->filter(fn($p) => str_starts_with($p->tanggal, $bulan));
            $h = $bulanIni->where('status', 'Hadir')->count();
            $i = $bulanIni->where('status', 'Izin')->count();
            $s = $bulanIni->where('status', 'Sakit')->count();
            $a = $bulanIni->where('status', 'Alpa')->count();

            $rekapKelasPerBulan[$bulan] = [
                'hari_aktif' => $hariAktif, 'slot' => $slot,
                'hadir' => $h, 'izin' => $i, 'sakit' => $s, 'alpa' => $a,
                'persen_hadir' => $persen($h, $slot), 'persen_izin' => $persen($i, $slot),
                'persen_sakit' => $persen($s, $slot), 'persen_alpa' => $persen($a, $slot),
            ];
        }

        // Rekap kelas total
        $slotTotal = $totalHariMasuk * $jumlahSiswa;
        $h = $presensi->where('status', 'Hadir')->count();
        $i = $presensi->where('status', 'Izin')->count();
        $s = $presensi->where('status', 'Sakit')->count();
        $a = $presensi->where('status', 'Alpa')->count();
        $rekapKelasTotal = [
            'slot' => $slotTotal,
            'hadir' => $h, 'izin' => $i, 'sakit' => $s, 'alpa' => $a,
            'persen_hadir' => $persen($h, $slotTotal), 'persen_izin' => $persen($i, $slotTotal),
            'persen_sakit' => $persen($s, $slotTotal), 'persen_alpa' => $persen($a, $slotTotal),
        ];

        // Rekap per siswa
        $rekap = [];
        foreach ($kelas->siswa as $siswa) {
            $presensiSiswa = $presensi->where('siswa_id', $siswa->id);

            $perBulan = [];
            foreach ($hariMasukPerBulan as $bulan => $hariMasuk) {
                $hadirBulanIni = $presensiSiswa
                    ->filter(fn($p) => str_starts_with($p->tanggal, $bulan) && $p->status === 'Hadir')
                    ->pluck('tanggal')->unique()->count();

                $perBulan[$bulan] = [
                    'hadir' => $hadirBulanIni,
                    'hari_masuk' => $hariMasuk,
                    'persen' => $persen($hadirBulanIni, $hariMasuk),
                ];
            }

            $totalHadir = $presensiSiswa->where('status', 'Hadir')->pluck('tanggal')->unique()->count();

            $rekap[] = (object) [
                'siswa' => $siswa,
                'per_bulan' => $perBulan,
                'total_hadir' => $totalHadir,
                'total_hari_masuk' => $totalHariMasuk,
                'total_persen' => $persen($totalHadir, $totalHariMasuk),
            ];
        }

        // Detail harian untuk 1 bulan
        $bulanTersedia = $hariMasukPerBulan->keys();
        $bulanDipilih = $bulanTersedia->contains($bulanReq) ? $bulanReq : $bulanTersedia->last();

        $tanggalList = collect();
        $detailHarian = [];

        if ($bulanDipilih) {
            $prioritas = ['Alpa' => 4, 'Sakit' => 3, 'Izin' => 2, 'Hadir' => 1];
            $presensiBulan = $presensi->filter(fn($p) => str_starts_with($p->tanggal, $bulanDipilih));
            $tanggalList = $presensiBulan->pluck('tanggal')->unique()->sort()->values();

            foreach ($presensiBulan->groupBy('siswa_id') as $siswaId => $barisSiswa) {
                foreach ($barisSiswa->groupBy('tanggal') as $tgl => $barisHari) {
                    $statusList = $barisHari->pluck('status');
                    $detailHarian[$siswaId][$tgl] = [
                        'status' => $statusList->sortByDesc(fn($st) => $prioritas[$st] ?? 0)->first(),
                        'rincian' => $statusList->countBy()->map(fn($n, $st) => "$st $n")->implode(', '),
                    ];
                }
            }
        }

        return compact(
            'hariMasukPerBulan', 'totalHariMasuk', 'rekap',
            'rekapKelasPerBulan', 'rekapKelasTotal',
            'bulanDipilih', 'tanggalList', 'detailHarian'
        );
    }
}