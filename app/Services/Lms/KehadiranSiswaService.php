<?php

namespace App\Services\Lms;

use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

/**
 * Persentase kehadiran seorang siswa per mapel & keseluruhan.
 *
 * Definisi "pertemuan" = tanggal di mana ada sesi presensi dibuka ATAU ada data
 * presensi (manual) untuk pengampu mapel kelasnya. Pertemuan tanpa catatan untuk
 * siswa tsb dihitung Alpa.
 *   persen = Hadir / Pertemuan × 100
 * Izin & Sakit tidak dihitung hadir (tampil terpisah di laporan).
 */
class KehadiranSiswaService
{
    public static function hitung(Siswa $siswa, string $tahunAjaran, string $semester): array
    {
        $pengampu = PengampuMapel::with('mataPelajaran')
            ->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get();

        $ids   = $pengampu->pluck('id');
        $today = now()->toDateString();

        // pertemuan per pengampu: [pengampu_id => [tanggal => true]]
        $pertemuan = [];
        $tambah = function ($pid, $tgl) use (&$pertemuan, $today) {
            $tgl = Carbon::parse($tgl)->toDateString();
            if ($tgl <= $today) {
                $pertemuan[$pid][$tgl] = true;
            }
        };

        SesiPresensi::whereIn('pengampu_mapel_id', $ids)->get(['pengampu_mapel_id', 'tanggal'])
            ->each(fn($r) => $tambah($r->pengampu_mapel_id, $r->tanggal));

        PresensiLms::whereIn('pengampu_mapel_id', $ids)->distinct()->get(['pengampu_mapel_id', 'tanggal'])
            ->each(fn($r) => $tambah($r->pengampu_mapel_id, $r->tanggal));

        // presensi milik siswa ini, per pengampu per tanggal
        $milikSiswa = PresensiLms::whereIn('pengampu_mapel_id', $ids)
            ->where('siswa_id', $siswa->id)
            ->get()
            ->groupBy('pengampu_mapel_id');

        $baris = [];
        $tot = ['pertemuan' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        foreach ($pengampu as $p) {
            $tanggalPertemuan = array_keys($pertemuan[$p->id] ?? []);
            $n = count($tanggalPertemuan);
            if ($n === 0) {
                continue; // mapel belum pernah ada pertemuan
            }

            $rows = ($milikSiswa[$p->id] ?? collect())
                ->filter(fn($r) => in_array(Carbon::parse($r->tanggal)->toDateString(), $tanggalPertemuan, true))
                ->unique(fn($r) => Carbon::parse($r->tanggal)->toDateString());

            $hadir = $rows->where('status', 'Hadir')->count();
            $izin  = $rows->where('status', 'Izin')->count();
            $sakit = $rows->where('status', 'Sakit')->count();
            $alpa  = max(0, $n - $hadir - $izin - $sakit);

            $baris[] = [
                'mapel'     => $p->mataPelajaran->nama ?? 'Mata Pelajaran',
                'pertemuan' => $n, 'hadir' => $hadir, 'izin' => $izin, 'sakit' => $sakit, 'alpa' => $alpa,
                'persen'    => round($hadir / $n * 100, 1),
            ];

            $tot['pertemuan'] += $n;
            $tot['hadir'] += $hadir;
            $tot['izin'] += $izin;
            $tot['sakit'] += $sakit;
            $tot['alpa'] += $alpa;
        }

        usort($baris, fn($a, $b) => strcmp($a['mapel'], $b['mapel']));
        $tot['persen'] = $tot['pertemuan'] > 0 ? round($tot['hadir'] / $tot['pertemuan'] * 100, 1) : null;

        return ['mapel' => $baris, 'total' => $tot];
    }

    /** Periode (tahun ajaran + semester) yang pernah ada untuk kelas siswa. */
    public static function periodeList(Siswa $siswa)
    {
        return PengampuMapel::where('kelas_id', $siswa->kelas_id)
            ->select('tahun_ajaran', 'semester')->distinct()
            ->orderByDesc('tahun_ajaran')->orderByDesc('semester')->get();
    }
}
