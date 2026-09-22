<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\WaliKelasPeriode;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('lms')->user();

        // PERBAIKAN: sebelumnya baca dari kolom LAMA kelas.wali_kelas_id
        // (global, gak ada dimensi tahun). Assignment wali kelas sekarang
        // disimpan di tabel wali_kelas_periode_lms (per tahun_ajaran +
        // semester) — jadi harus dibaca dari situ.
        $penugasanWali = WaliKelasPeriode::where('user_id', $user->id)
            ->with('kelas')
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        abort_if($penugasanWali->isEmpty(), 403, 'Anda bukan wali kelas manapun.');

        $kelasDiwalikan = $penugasanWali->pluck('kelas')->unique('id')->values();

        $kelasId = (int) $request->query('kelas_id', $kelasDiwalikan->first()->id);
        $kelas = $kelasDiwalikan->firstWhere('id', $kelasId) ?? $kelasDiwalikan->first();

        $kelas->load('siswa');

        $periodeList = PengampuMapel::where('kelas_id', $kelas->id)
            ->select('tahun_ajaran', 'semester')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        $tahunAjaran = $request->query('tahun_ajaran', $periodeList->first()->tahun_ajaran ?? null);
        $semester = $request->query('semester', $periodeList->first()->semester ?? null);

        $daftarPengampu = PengampuMapel::with('mataPelajaran', 'guru')
            ->where('kelas_id', $kelas->id)
            ->when($tahunAjaran, fn($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->get();

        $rekapPerMapel = [];
        foreach ($daftarPengampu as $pengampu) {
            $pengampu->setRelation('kelas', $kelas);
            $hasil = NilaiAkhirService::hitung($pengampu);
            $rekapPerMapel[$pengampu->id] = $hasil['siswa'];
        }

        return view('lms.guru.wali-kelas', compact(
            'kelasDiwalikan', 'kelas', 'daftarPengampu', 'rekapPerMapel',
            'periodeList', 'tahunAjaran', 'semester'
        ));
    }
}