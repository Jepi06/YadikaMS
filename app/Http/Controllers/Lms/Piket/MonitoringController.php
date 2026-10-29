<?php

namespace App\Http\Controllers\Lms\Piket;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Services\Lms\MonitoringPresensiService;
use App\Services\Lms\PelanggaranAkses as Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/** Monitoring presensi hari ini untuk Guru Piket (login lewat LMS), dengan filter kelas. */
class MonitoringController extends Controller
{
    private function izin(): void
    {
        abort_unless(
            Akses::bolehMonitoring(Auth::guard('lms')->user()),
            403,
            'Halaman ini khusus Guru Piket.'
        );
    }

    public function index(Request $request, MonitoringPresensiService $svc)
    {
        $this->izin();

        $kelasList = Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']);
        $kelasId   = (int) $request->query('kelas_id') ?: null;

        $data = $svc->susun($kelasId);

        return view('lms.piket.monitoring', $data + compact('kelasList', 'kelasId'));
    }

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
