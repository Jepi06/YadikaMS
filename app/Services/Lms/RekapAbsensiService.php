<?php

namespace App\Services\Lms;

use App\Models\Kelas;
use App\Models\Lms\KoreksiAbsensiHarian;
use App\Models\Lms\PengampuMapel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RekapAbsensiService
{
    /** Angka lebih besar = menang. */
    public const PRIORITAS = ['Hadir' => 4, 'Alpa' => 3, 'Sakit' => 2, 'Izin' => 1];

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
     * Status harian OTOMATIS (tanpa koreksi) untuk 1 siswa di 1 tanggal.
     * Dipakai controller koreksi sebagai snapshot "status_sistem".
     * Return null kalau tidak ada presensi sama sekali di tanggal itu.
     */
    public static function statusSistem(Kelas $kelas, $tahunAjaran, $semester, int $siswaId, string $tanggal): ?string
    {
        $ids = PengampuMapel::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->pluck('id');

        $status = DB::table('presensi_lms')
            ->whereIn('pengampu_mapel_id', $ids)
            ->where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->pluck('status');

        if ($status->isEmpty()) {
            return null;
        }

        return $status->sortByDesc(fn($s) => self::PRIORITAS[$s] ?? 0)->first();
    }

    /** Koreksi aktif satu kelas+periode, key: "siswa_id|Y-m-d". */
    private static function koreksiMap(Kelas $kelas, $tahunAjaran, $semester): Collection
    {
        return KoreksiAbsensiHarian::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get()
            ->keyBy(fn($k) => $k->siswa_id . '|' . Carbon::parse($k->tanggal)->format('Y-m-d'));
    }

    /**
     * Catatan untuk hari yang (menurut sistem) HADIR tapi ada mapel lain tidak Hadir.
     * jenis:
     *   terlambat → tidak-hadir tercatat SEBELUM presensi Hadir pertama (telat / tidak ada di jam awal)
     *   keluar    → tidak-hadir tercatat SESUDAH presensi Hadir pertama (keluar di tengah)
     *   lainnya   → waktu tidak tersedia / sama persis (tidak bisa ditentukan)
     * Catatan: urutan memakai waktu PENCATATAN presensi, bukan jam pelajaran ke-N.
     */
    private static function susunCatatan(Collection $barisHari, \Closure $namaMapel, \Closure $namaGuru): array
    {
        $urut = $barisHari->sortBy(fn($x) => $x->jam ?? '99:99')->values();
        $jamHadirPertama = $urut->first(fn($x) => $x->status === 'Hadir')->jam ?? null;

        return $urut
            ->filter(fn($x) => $x->status !== 'Hadir')
            ->map(function ($x) use ($jamHadirPertama, $namaMapel, $namaGuru) {
                $jenis = 'lainnya';
                if ($x->jam && $jamHadirPertama) {
                    if ($x->jam < $jamHadirPertama) {
                        $jenis = 'terlambat';
                    } elseif ($x->jam > $jamHadirPertama) {
                        $jenis = 'keluar';
                    }
                }

                return [
                    'status' => $x->status,
                    'mapel' => $namaMapel($x->pengampu_mapel_id),
                    'guru' => $namaGuru($x->pengampu_mapel_id),
                    'jam' => $x->jam,
                    'jenis' => $jenis,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Susun semua data rekap absensi 1 kelas.
     *
     * ATURAN HARIAN (banyak guru/mapel dalam 1 hari → dihitung 1):
     *   - Hadir di salah satu mapel  → hari itu HADIR (terlambat tetap Hadir, ada catatan)
     *   - Tidak ada yang Hadir       → status terberat: Alpa > Sakit > Izin
     *   - Kalau hari itu HADIR tapi di mapel lain Alpa/Izin/Sakit → dicatat di
     *     $catatanHarian dengan jenis (terlambat / keluar / lainnya).
     *   - KOREKSI wali kelas (tabel koreksi_absensi_harian_lms) menimpa status harian.
     *     Data presensi asli guru mapel tidak pernah diubah.
     */
    public static function susun(Kelas $kelas, $tahunAjaran, $semester, ?string $bulanReq = null): array
    {
        $kelas->loadMissing('siswa');

        // Eager load relasi (lazy loading dimatikan di project ini)
        $pengampuList = PengampuMapel::with('mataPelajaran', 'guru')
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get()
            ->keyBy('id');

        $pengampuIds = $pengampuList->keys();

        $namaMapel = function ($pid) use ($pengampuList) {
            $m = $pengampuList[$pid]->mataPelajaran ?? null;
            return $m->nama_mapel ?? ($m->nama ?? 'Mapel');
        };
        $namaGuru = function ($pid) use ($pengampuList) {
            $g = $pengampuList[$pid]->guru ?? null;
            return $g->nama ?? ($g->name ?? '-');
        };

        $prioritas = self::PRIORITAS;
        $koreksiMap = self::koreksiMap($kelas, $tahunAjaran, $semester);

        // Kolom waktu presensi: pakai kolom jam kalau ada, kalau tidak created_at.
        $kolomWaktu = collect(['jam_presensi', 'waktu_presensi', 'jam', 'waktu', 'created_at'])
            ->first(fn($c) => Schema::hasColumn('presensi_lms', $c));

        $kolom = ['pengampu_mapel_id', 'siswa_id', 'tanggal', 'status'];
        if ($kolomWaktu) {
            $kolom[] = $kolomWaktu;
        }

        // Data mentah (1 baris per mapel)
        $presensiMentah = DB::table('presensi_lms')
            ->whereIn('pengampu_mapel_id', $pengampuIds)
            ->get($kolom)
            ->map(function ($p) use ($kolomWaktu) {
                $p->tanggal = Carbon::parse($p->tanggal)->format('Y-m-d');
                $p->jam = null;
                if ($kolomWaktu && !empty($p->{$kolomWaktu})) {
                    try {
                        $p->jam = Carbon::parse($p->{$kolomWaktu})->format('H:i');
                    } catch (\Throwable $e) {
                        $p->jam = null;
                    }
                }
                return $p;
            });

        // Data harian → 1 baris per siswa per tanggal (sudah memperhitungkan koreksi)
        $presensi = $presensiMentah
            ->groupBy(fn($p) => $p->siswa_id . '|' . $p->tanggal)
            ->map(function ($grup, $key) use ($prioritas, $koreksiMap) {
                $p = $grup->sortByDesc(fn($x) => $prioritas[$x->status] ?? 0)->first();
                $koreksi = $koreksiMap[$key] ?? null;

                return (object) [
                    'siswa_id' => $p->siswa_id,
                    'tanggal' => $p->tanggal,
                    'status_sistem' => $p->status,
                    'status' => $koreksi ? $koreksi->status_koreksi : $p->status,
                ];
            })
            ->values();

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
                    ->count();

                $perBulan[$bulan] = [
                    'hadir' => $hadirBulanIni,
                    'hari_masuk' => $hariMasuk,
                    'persen' => $persen($hadirBulanIni, $hariMasuk),
                ];
            }

            $totalHadir = $presensiSiswa->where('status', 'Hadir')->count();

            $rekap[] = (object) [
                'siswa' => $siswa,
                'per_bulan' => $perBulan,
                'total_hadir' => $totalHadir,
                'total_hari_masuk' => $totalHariMasuk,
                'total_persen' => $persen($totalHadir, $totalHariMasuk),
            ];
        }

        // Detail harian 1 bulan + catatan + daftar koreksi bulan itu
        $bulanTersedia = $hariMasukPerBulan->keys();
        $bulanDipilih = $bulanTersedia->contains($bulanReq) ? $bulanReq : $bulanTersedia->last();

        $tanggalList = collect();
        $detailHarian = [];
        $catatanHarian = []; // list: [siswa_id, tanggal, entries[[status, mapel, guru, jam, jenis]]]
        $koreksiBulan = [];  // list koreksi pada bulan terpilih

        if ($bulanDipilih) {
            $presensiBulan = $presensiMentah->filter(fn($p) => str_starts_with($p->tanggal, $bulanDipilih));
            $tanggalList = $presensiBulan->pluck('tanggal')->unique()->sort()->values();

            foreach ($presensiBulan->groupBy('siswa_id') as $siswaId => $barisSiswa) {
                foreach ($barisSiswa->groupBy('tanggal') as $tgl => $barisHari) {
                    $statusList = $barisHari->pluck('status');
                    $sistem = $statusList->sortByDesc(fn($st) => $prioritas[$st] ?? 0)->first();
                    $koreksi = $koreksiMap[$siswaId . '|' . $tgl] ?? null;

                    $catatan = $sistem === 'Hadir'
                        ? self::susunCatatan($barisHari, $namaMapel, $namaGuru)
                        : [];

                    $detailHarian[$siswaId][$tgl] = [
                        'status' => $koreksi ? $koreksi->status_koreksi : $sistem,
                        'status_sistem' => $sistem,
                        'dikoreksi' => (bool) $koreksi,
                        'koreksi' => $koreksi ? [
                            'id' => $koreksi->id,
                            'alasan' => $koreksi->alasan,
                            'oleh' => $koreksi->diubah_oleh_nama,
                            'waktu' => $koreksi->updated_at?->format('d M Y H:i'),
                        ] : null,
                        'rincian' => $statusList->countBy()->map(fn($n, $st) => "$st $n")->implode(', '),
                        'catatan' => $catatan,
                    ];

                    if (!empty($catatan)) {
                        $catatanHarian[] = [
                            'siswa_id' => $siswaId,
                            'tanggal' => $tgl,
                            'entries' => $catatan,
                        ];
                    }

                    if ($koreksi) {
                        $koreksiBulan[] = [
                            'id' => $koreksi->id,
                            'siswa_id' => $siswaId,
                            'tanggal' => $tgl,
                            'status_sistem' => $koreksi->status_sistem,
                            'status_koreksi' => $koreksi->status_koreksi,
                            'alasan' => $koreksi->alasan,
                            'oleh' => $koreksi->diubah_oleh_nama,
                            'waktu' => $koreksi->updated_at?->format('d M Y H:i'),
                        ];
                    }
                }
            }

            usort($catatanHarian, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));
            usort($koreksiBulan, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));
        }

        return compact(
            'hariMasukPerBulan', 'totalHariMasuk', 'rekap',
            'rekapKelasPerBulan', 'rekapKelasTotal',
            'bulanDipilih', 'tanggalList', 'detailHarian', 'catatanHarian', 'koreksiBulan'
        );
    }
}