<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PelanggaranSiswa;
use App\Models\Lms\WaliKelasPeriode;
use App\Models\Siswa;
use App\Services\Lms\KehadiranSiswaService;
use App\Services\Lms\PelanggaranService;
use App\Support\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Laporan kehadiran + poin pelanggaran milik siswa sendiri (tampil & unduh PDF).
 */
class LaporanController extends Controller
{
    private function data(Request $request): array
    {
        $user  = Auth::guard('lms')->user();
        $siswa = Siswa::with('kelas')->where('user_id', $user->id)->firstOrFail();

        $periodeList = KehadiranSiswaService::periodeList($siswa);
        $tahunAjaran = $request->query('tahun_ajaran', $periodeList->first()->tahun_ajaran ?? TahunAjaran::sekarang());
        $semester    = $request->query('semester', $periodeList->first()->semester ?? TahunAjaran::semesterSekarang());

        $kehadiran = KehadiranSiswaService::hitung($siswa, $tahunAjaran, $semester);

        $poin = PelanggaranService::ringkasan($siswa, 0);
        $riwayat = PelanggaranSiswa::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')->orderByDesc('id')->get();

        $wali = WaliKelasPeriode::with('user')
            ->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first()?->user?->name;

        return compact('siswa', 'periodeList', 'tahunAjaran', 'semester', 'kehadiran', 'poin', 'riwayat', 'wali');
    }

    public function index(Request $request)
    {
        return view('lms.siswa.laporan', $this->data($request));
    }

    public function pdf(Request $request)
    {
        $data = $this->data($request);
        $data['dicetak'] = now();

        $nama = Str::slug($data['siswa']->nama) . '-laporan-' . Str::slug($data['tahunAjaran'] . '-' . $data['semester']) . '.pdf';

        return Pdf::loadView('lms.siswa.laporan-pdf', $data)->setPaper('a4')->download($nama);
    }
}
