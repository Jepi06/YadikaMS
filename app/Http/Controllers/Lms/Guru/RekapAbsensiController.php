<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Services\Lms\RekapAbsensiService;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Admin bisa pilih SEMUA kelas
        $kelasDiwalikan = Kelas::orderBy('nama_kelas')->get();
        abort_if($kelasDiwalikan->isEmpty(), 404, 'Belum ada data kelas.');

        $kelasId = (int) $request->query('kelas_id', $kelasDiwalikan->first()->id);
        $kelas = $kelasDiwalikan->firstWhere('id', $kelasId) ?? $kelasDiwalikan->first();

        $periodeList = RekapAbsensiService::periodeList($kelas);
        $tahunAjaran = $request->query('tahun_ajaran', $periodeList->first()->tahun_ajaran ?? TahunAjaran::sekarang());
        $semester = $request->query('semester', $periodeList->first()->semester ?? TahunAjaran::semesterSekarang());

        $data = RekapAbsensiService::susun($kelas, $tahunAjaran, $semester, $request->query('bulan'));

        return view('lms.guru.wali-kelas-absensi', $data + compact(
            'kelasDiwalikan', 'kelas', 'periodeList', 'tahunAjaran', 'semester'
        ) + [
            'modeAdmin' => true,
            'layout' => 'superadmin.layouts.app', // ← GANTI sesuai nama layout panel admin kamu
        ]);
    }
}