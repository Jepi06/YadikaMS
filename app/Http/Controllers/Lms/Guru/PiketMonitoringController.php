<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Services\Lms\MonitoringPresensiService;
use App\Services\Lms\PelanggaranAkses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Monitoring presensi hari ini untuk Guru Piket (login LMS).
 * Logika sama dengan SuperAdmin\MonitoringPresensiController (ada di
 * MonitoringPresensiService) + filter kelas + pengecekan akses.
 */
class PiketMonitoringController extends Controller
{
    private function izin(): void
    {
        abort_unless(
            PelanggaranAkses::bolehMonitoring(Auth::guard('lms')->user()),
            403,
            'Halaman ini khusus Guru Piket.'
        );
    }

    public function index(Request $request, MonitoringPresensiService $svc)
    {
        $this->izin();

        $kelasList = Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']);
        $kelasId   = (int) $request->query('kelas_id') ?: null;

        return view('lms.piket.monitoring', $svc->susun($kelasId) + compact('kelasList', 'kelasId'));
    }

    /** Detail: siapa saja siswa yang sudah dan belum presensi di 1 kelas/mapel. */
    public function detail(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->izin();

        $tanggal = $request->query('tanggal');
        $tanggal = ($tanggal && Carbon::hasFormat($tanggal, 'Y-m-d')) ? $tanggal : now()->toDateString();

        $pengampuMapel->load(['guru', 'mataPelajaran', 'kelas.siswa']);

        $presensi = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)->get()->keyBy('siswa_id');

        $sesi = SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)->first();

        $rows = $pengampuMapel->kelas->siswa
            ->map(fn($s) => ['siswa' => $s, 'presensi' => $presensi->get($s->id)])
            ->sortBy(fn($r) => $r['presensi'] ? 1 : 0)
            ->values();

        $sudah = $rows->filter(fn($r) => $r['presensi'])->count();
        $belum = $rows->count() - $sudah;

        return view('lms.piket.monitoring-detail', compact('pengampuMapel', 'tanggal', 'sesi', 'rows', 'sudah', 'belum'));
    }
}