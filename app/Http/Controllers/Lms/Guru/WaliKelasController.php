<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasController extends Controller
{
    /**
     * Rekap nilai akhir SEMUA mata pelajaran untuk kelas yang diwali-i
     * user ini (dicek lewat kolom kelas.wali_kelas_id — status wali
     * kelas itu independen dari role LMS, jadi siapa pun guru yang
     * ke-set sebagai wali_kelas_id boleh buka halaman ini).
     */
    public function index(Request $request)
    {
        $user = Auth::guard('lms')->user();

        $kelasDiwalikan = Kelas::where('wali_kelas_id', $user->id)
            ->orderBy('nama_kelas')
            ->get();

        abort_if($kelasDiwalikan->isEmpty(), 403, 'Anda bukan wali kelas manapun.');

        $kelasId = (int) $request->query('kelas_id', $kelasDiwalikan->first()->id);
        $kelas = $kelasDiwalikan->firstWhere('id', $kelasId) ?? $kelasDiwalikan->first();

        $kelas->load('siswa');

        $daftarPengampu = PengampuMapel::with('mataPelajaran', 'guru')
            ->where('kelas_id', $kelas->id)
            ->get();

        // Hitung nilai akhir per mapel. $pengampu->kelas di-set manual
        // ke $kelas yang sudah eager-load siswa, biar NilaiAkhirService
        // gak query ulang siswa per mapel.
        $rekapPerMapel = [];
        foreach ($daftarPengampu as $pengampu) {
            $pengampu->setRelation('kelas', $kelas);
            $hasil = NilaiAkhirService::hitung($pengampu);
            $rekapPerMapel[$pengampu->id] = $hasil['siswa'];
        }

        return view('lms.guru.wali-kelas', compact(
            'kelasDiwalikan', 'kelas', 'daftarPengampu', 'rekapPerMapel'
        ));
    }
}
