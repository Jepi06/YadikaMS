<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\JadwalPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MonitoringPresensiController extends Controller
{
    public function index()
    {
        $hariIni = now()->toDateString();

        // Semua sesi hari ini (aktif maupun sudah ditutup)
        $sesiHariIni = SesiPresensi::whereDate('tanggal', $hariIni)->get()->keyBy('pengampu_mapel_id');

        $sesiAktif = $sesiHariIni->filter(fn($s) => $s->dibuka_at <= now() && $s->ditutup_at > now());

        $sudahPresensi = PresensiLms::whereDate('tanggal', $hariIni)
            ->selectRaw('pengampu_mapel_id, COUNT(*) as total')
            ->groupBy('pengampu_mapel_id')
            ->pluck('total', 'pengampu_mapel_id');

        // ── A. Jadwal hari ini vs realisasi presensi ───────────
        $urutan = ['belum_buka' => 0, 'terlewat' => 1, 'dibuka' => 2, 'menunggu' => 3];

        $jadwalHariIni = JadwalPelajaran::with([
            'pengampuMapel.guru',
            'pengampuMapel.mataPelajaran',
            'pengampuMapel.kelas' => fn($q) => $q->withCount('siswa'),
        ])
            ->where('hari', now()->dayOfWeekIso)
            ->whereHas('pengampuMapel', fn($q) => $q
                ->where('tahun_ajaran', TahunAjaran::sekarang())
                ->where('semester', TahunAjaran::semesterSekarang()))
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($j) use ($hariIni, $sesiHariIni, $sesiAktif, $sudahPresensi) {
                $p       = $j->pengampuMapel;
                $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}");
                $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");
                $sesi    = $sesiHariIni->get($p->id);

                $status = match (true) {
                    (bool) $sesi        => 'dibuka',
                    now()->lt($mulai)   => 'menunggu',
                    now()->lt($selesai) => 'belum_buka',
                    default             => 'terlewat',
                };

                return [
                    'pengampu_id' => $p->id,
                    'guru'        => $p->guru->name ?? '-',
                    'mapel'       => $p->mataPelajaran->nama ?? '-',
                    'kelas'       => $p->kelas->nama_kelas ?? '-',
                    'ruangan'     => $j->ruangan,
                    'mulai'       => substr($j->jam_mulai, 0, 5),
                    'selesai'     => substr($j->jam_selesai, 0, 5),
                    'status'      => $status,
                    'sesi_aktif'  => $sesiAktif->has($p->id),
                    'dibuka_at'   => $sesi?->dibuka_at,
                    'sudah'       => $sudahPresensi[$p->id] ?? 0,
                    'total'       => $p->kelas->siswa_count ?? 0,
                ];
            })
            ->sortBy(fn($r) => $urutan[$r['status']])
            ->values();

        $ringkas = $jadwalHariIni->countBy('status');

        // ── B. Status per guru (dengan peringatan jadwal) ───────
        $jadwalMap = JadwalPelajaran::where('hari', now()->dayOfWeekIso)
            ->get()->groupBy('pengampu_mapel_id');

        $pengampu = PengampuMapel::with(['guru', 'mataPelajaran', 'kelas' => fn($q) => $q->withCount('siswa')])->get();

        $daftarGuru = $pengampu->groupBy('guru_id')
            ->map(function ($items) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni) {

                $semuaKelas = $items->map(function ($p) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni) {
                    $sesi = $sesiAktif->get($p->id);

                    $jadwalStatus = null;
                    $jadwalJam = null;
                    $rank = ['belum_buka' => 0, 'terlewat' => 1, 'dibuka' => 2, 'menunggu' => 3];

                    foreach ($jadwalMap->get($p->id, collect()) as $j) {
                        $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}");
                        $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");

                        $st = match (true) {
                            $sesiHariIni->has($p->id) => 'dibuka',
                            now()->lt($mulai)         => 'menunggu',
                            now()->lt($selesai)       => 'belum_buka',
                            default                   => 'terlewat',
                        };

                        if ($jadwalStatus === null || $rank[$st] < $rank[$jadwalStatus]) {
                            $jadwalStatus = $st;
                            $jadwalJam = substr($j->jam_mulai, 0, 5) . '–' . substr($j->jam_selesai, 0, 5);
                        }
                    }

                    return [
                        'pengampu_id'   => $p->id,
                        'kelas'         => $p->kelas->nama_kelas ?? '-',
                        'mapel'         => $p->mataPelajaran->nama ?? '-',
                        'aktif'         => (bool) $sesi,
                        'ditutup_at'    => $sesi?->ditutup_at,
                        'sudah'         => $sudahPresensi[$p->id] ?? 0,
                        'total'         => $p->kelas->siswa_count ?? 0,
                        'jadwal_status' => $jadwalStatus,
                        'jadwal_jam'    => $jadwalJam,
                    ];
                })->sortBy(fn($k) => match (true) {
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

                return [
                    'guru'   => $items->first()->guru,
                    'status' => $status,
                    'kelas'  => $semuaKelas,
                ];
            })
            ->filter(fn($row) => $row['guru'])
            ->sortBy(fn($row) => ['harus_buka' => 0, 'terlewat' => 1, 'buka' => 2, 'tutup' => 3][$row['status']])
            ->values();

        $jumlahBuka      = $daftarGuru->where('status', 'buka')->count();
        $jumlahHarusBuka = $daftarGuru->where('status', 'harus_buka')->count();
        $jumlahTerlewat  = $daftarGuru->where('status', 'terlewat')->count();
        $jumlahTutup     = $daftarGuru->where('status', 'tutup')->count();

        return view('admin.monitoring-presensi', compact(
            'jadwalHariIni',
            'ringkas',
            'daftarGuru',
            'jumlahBuka',
            'jumlahHarusBuka',
            'jumlahTerlewat',
            'jumlahTutup'
        ));
    }

    /** Detail: siapa saja siswa yang sudah dan belum presensi di 1 kelas/mapel. */
    public function detail(Request $request, PengampuMapel $pengampuMapel)
    {
        $tanggal = $request->query('tanggal');
        $tanggal = ($tanggal && Carbon::hasFormat($tanggal, 'Y-m-d')) ? $tanggal : now()->toDateString();

        $pengampuMapel->load(['guru', 'mataPelajaran', 'kelas.siswa']);

        $presensi = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $sesi = SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        $rows = $pengampuMapel->kelas->siswa
            ->map(fn($s) => ['siswa' => $s, 'presensi' => $presensi->get($s->id)])
            ->sortBy(fn($r) => $r['presensi'] ? 1 : 0)   // yang belum presensi di atas
            ->values();

        $sudah = $rows->filter(fn($r) => $r['presensi'])->count();
        $belum = $rows->count() - $sudah;

        return view('admin.monitoring-presensi-detail', compact(
            'pengampuMapel',
            'tanggal',
            'sesi',
            'rows',
            'sudah',
            'belum'
        ));
    }
}
