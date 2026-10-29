<?php

namespace App\Services\Lms;

use App\Models\Lms\JadwalPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Support\TahunAjaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Logika monitoring presensi hari ini (sama dengan SuperAdmin\MonitoringPresensiController)
 * + filter kelas, dipakai oleh Guru Piket.
 */
class MonitoringPresensiService
{
    private const TOLERANSI_MENIT = 15;

    private function jadwalPemilikSesi(?SesiPresensi $sesi, Collection $slots, string $hariIni): ?int
    {
        if (! $sesi || ! $sesi->dibuka_at || $slots->isEmpty()) {
            return null;
        }

        $dibuka = Carbon::parse($sesi->dibuka_at);

        foreach ($slots as $j) {
            $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}")->subMinutes(self::TOLERANSI_MENIT);
            $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");
            if ($dibuka->gte($mulai) && $dibuka->lt($selesai)) {
                return $j->id;
            }
        }

        $berikut = $slots->first(fn($j) => $dibuka->lt(Carbon::parse("{$hariIni} {$j->jam_selesai}")));

        return ($berikut ?? $slots->last())->id;
    }

    private function statusJadwal($j, ?int $idPemilikSesi, string $hariIni): string
    {
        $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}");
        $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");

        return match (true) {
            $idPemilikSesi !== null && $idPemilikSesi === $j->id => 'dibuka',
            now()->lt($mulai)                                    => 'menunggu',
            now()->lt($selesai)                                  => 'belum_buka',
            default                                              => 'terlewat',
        };
    }

    public function susun(?int $kelasId = null): array
    {
        $hariIni = now()->toDateString();
        $ta  = TahunAjaran::sekarang();
        $smt = TahunAjaran::semesterSekarang();

        $sesiHariIni = SesiPresensi::whereDate('tanggal', $hariIni)->get()->keyBy('pengampu_mapel_id');
        $sesiAktif   = $sesiHariIni->filter(fn($s) => $s->dibuka_at <= now() && $s->ditutup_at > now());

        $sudahPresensi = PresensiLms::whereDate('tanggal', $hariIni)
            ->selectRaw('pengampu_mapel_id, COUNT(*) as total')
            ->groupBy('pengampu_mapel_id')
            ->pluck('total', 'pengampu_mapel_id');

        $jadwalMap = JadwalPelajaran::where('hari', now()->dayOfWeekIso)
            ->orderBy('jam_mulai')->get()->groupBy('pengampu_mapel_id');

        $urutan = ['belum_buka' => 0, 'terlewat' => 1, 'dibuka' => 2, 'menunggu' => 3];

        // ── A. Jadwal hari ini vs realisasi ──
        $jadwalHariIni = JadwalPelajaran::with([
            'pengampuMapel.guru',
            'pengampuMapel.mataPelajaran',
            'pengampuMapel.kelas' => fn($q) => $q->withCount('siswa'),
        ])
            ->where('hari', now()->dayOfWeekIso)
            ->whereHas('pengampuMapel', fn($q) => $q
                ->where('tahun_ajaran', $ta)
                ->where('semester', $smt)
                ->when($kelasId, fn($q) => $q->where('kelas_id', $kelasId)))
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($j) use ($hariIni, $sesiHariIni, $sesiAktif, $sudahPresensi, $jadwalMap) {
                $p      = $j->pengampuMapel;
                $sesi   = $sesiHariIni->get($p->id);
                $idPem  = $this->jadwalPemilikSesi($sesi, $jadwalMap->get($p->id, collect()), $hariIni);
                $status = $this->statusJadwal($j, $idPem, $hariIni);
                $dibuka = $status === 'dibuka';

                return [
                    'pengampu_id' => $p->id,
                    'guru'        => $p->guru->name ?? '-',
                    'mapel'       => $p->mataPelajaran->nama ?? '-',
                    'kelas'       => $p->kelas->nama_kelas ?? '-',
                    'ruangan'     => $j->ruangan,
                    'mulai'       => substr($j->jam_mulai, 0, 5),
                    'selesai'     => substr($j->jam_selesai, 0, 5),
                    'status'      => $status,
                    'sesi_aktif'  => $dibuka && $sesiAktif->has($p->id),
                    'dibuka_at'   => $dibuka ? $sesi?->dibuka_at : null,
                    'sudah'       => $dibuka ? ($sudahPresensi[$p->id] ?? 0) : null,
                    'total'       => $p->kelas->siswa_count ?? 0,
                ];
            })
            ->sortBy(fn($r) => $urutan[$r['status']])
            ->values();

        $ringkas = $jadwalHariIni->countBy('status');

        // ── B. Status per guru ──
        $pengampu = PengampuMapel::with(['guru', 'mataPelajaran', 'kelas' => fn($q) => $q->withCount('siswa')])
            ->when($kelasId, fn($q) => $q->where('kelas_id', $kelasId))
            ->get();

        $daftarGuru = $pengampu->groupBy('guru_id')
            ->map(function ($items) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni, $urutan) {
                $semuaKelas = $items->map(function ($p) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni, $urutan) {
                    $sesiA = $sesiAktif->get($p->id);
                    $slots = $jadwalMap->get($p->id, collect());
                    $idPem = $this->jadwalPemilikSesi($sesiHariIni->get($p->id), $slots, $hariIni);

                    $jadwalStatus = null;
                    $jadwalJam = null;
                    foreach ($slots as $j) {
                        $st = $this->statusJadwal($j, $idPem, $hariIni);
                        if ($jadwalStatus === null || $urutan[$st] < $urutan[$jadwalStatus]) {
                            $jadwalStatus = $st;
                            $jadwalJam = substr($j->jam_mulai, 0, 5) . '–' . substr($j->jam_selesai, 0, 5);
                        }
                    }

                    return [
                        'pengampu_id'   => $p->id,
                        'kelas'         => $p->kelas->nama_kelas ?? '-',
                        'mapel'         => $p->mataPelajaran->nama ?? '-',
                        'aktif'         => (bool) $sesiA,
                        'ditutup_at'    => $sesiA?->ditutup_at,
                        'sudah'         => $sudahPresensi[$p->id] ?? 0,
                        'total'         => $p->kelas->siswa_count ?? 0,
                        'jadwal_status' => $jadwalStatus,
                        'jadwal_jam'    => $jadwalJam,
                    ];
                })
                    // dengan filter kelas, tampilkan hanya yang punya jadwal hari ini / sesi aktif
                    ->sortBy(fn($k) => match (true) {
                        $k['aktif']                          => 0,
                        $k['jadwal_status'] === 'belum_buka' => 1,
                        $k['jadwal_status'] === 'terlewat'   => 2,
                        $k['jadwal_status'] !== null         => 3,
                        default                              => 4,
                    })->values();

                $status = match (true) {
                    $semuaKelas->contains('aktif', true)                 => 'buka',
                    $semuaKelas->contains('jadwal_status', 'belum_buka') => 'harus_buka',
                    $semuaKelas->contains('jadwal_status', 'terlewat')   => 'terlewat',
                    default                                              => 'tutup',
                };

                return ['guru' => $items->first()->guru, 'status' => $status, 'kelas' => $semuaKelas];
            })
            ->filter(fn($row) => $row['guru'])
            ->sortBy(fn($row) => ['harus_buka' => 0, 'terlewat' => 1, 'buka' => 2, 'tutup' => 3][$row['status']])
            ->values();

        return [
            'jadwalHariIni'   => $jadwalHariIni,
            'ringkas'         => $ringkas,
            'daftarGuru'      => $daftarGuru,
            'jumlahBuka'      => $daftarGuru->where('status', 'buka')->count(),
            'jumlahHarusBuka' => $daftarGuru->where('status', 'harus_buka')->count(),
            'jumlahTerlewat'  => $daftarGuru->where('status', 'terlewat')->count(),
            'jumlahTutup'     => $daftarGuru->where('status', 'tutup')->count(),
        ];
    }
}
