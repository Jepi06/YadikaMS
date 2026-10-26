<?php

namespace App\Services\Lms;

use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use Illuminate\Support\Facades\DB;

/**
 * Satu tempat buat hitung Nilai Akhir, dipakai oleh:
 * - Lms\Guru\NilaiController (rekap 1 mapel, guru pengampu)
 * - Lms\Guru\WaliKelasController (rekap lintas mapel, buat wali kelas)
 * supaya rumusnya konsisten di mana pun dipakai.
 */
class NilaiAkhirService
{
    /** Konversi predikat sikap (kualitatif) ke angka, biar bisa dihitung. */
    public const PREDIKAT_NUMERIK = [
        'Sangat Baik' => 90,
        'Baik' => 80,
        'Cukup' => 70,
        'Kurang' => 60,
    ];

    /**
     * Hitung nilai akhir semua siswa di 1 pengampu_mapel.
     *
     * Rata-rata tugas = TOTAL nilai tugas siswa / JUMLAH SELURUH TUGAS mapel ini.
     * Tugas yang belum dikumpulkan / belum dinilai dihitung 0.
     *
     * Return array:
     * [
     *   'bobot' => BobotNilai,
     *   'siswa' => Collection keyed by siswa_id, tiap item stdClass:
     *              siswa, rata_tugas, jumlah_tugas, jumlah_tugas_dinilai,
     *              nilai_sts, nilai_sas, sikap_predikat, sikap_catatan,
     *              nilai_akhir, lengkap (bool)
     * ]
     */
    public static function hitung(PengampuMapel $pengampuMapel): array
    {
        if (! $pengampuMapel->relationLoaded('kelas') || ! $pengampuMapel->kelas->relationLoaded('siswa')) {
            $pengampuMapel->load('kelas.siswa');
        }

        $bobot = BobotNilai::firstOrCreate(
            ['pengampu_mapel_id' => $pengampuMapel->id],
            ['bobot_tugas' => 40, 'bobot_sts' => 20, 'bobot_sas' => 20, 'bobot_sikap' => 20]
        );

        // Pembagi rata-rata: jumlah SELURUH tugas di mapel ini.
        // Kalau mau hanya tugas yang deadline-nya sudah lewat, tambahkan:
        // ->where('batas_waktu', '<', now())
        $jumlahTugas = DB::table('tugas')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->count();

        $totalTugas = DB::table('pengumpulan_tugas')
            ->join('tugas', 'tugas.id', '=', 'pengumpulan_tugas.tugas_id')
            ->where('tugas.pengampu_mapel_id', $pengampuMapel->id)
            ->whereNotNull('pengumpulan_tugas.dinilai_at')
            ->whereNotNull('pengumpulan_tugas.nilai')
            ->groupBy('pengumpulan_tugas.siswa_id')
            ->select(
                'pengumpulan_tugas.siswa_id',
                DB::raw('SUM(pengumpulan_tugas.nilai) as total_nilai'),
                DB::raw('COUNT(*) as jumlah_dinilai')
            )
            ->get()
            ->keyBy('siswa_id');

        $nilaiUjian = NilaiUjian::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');
        $nilaiSikap = NilaiSikap::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');

        $hasil = collect();

        foreach ($pengampuMapel->kelas->siswa as $siswa) {
            $rt = $totalTugas->get($siswa->id);
            $uj = $nilaiUjian->get($siswa->id);
            $sk = $nilaiSikap->get($siswa->id);

            $jumlahDinilai = (int) ($rt->jumlah_dinilai ?? 0);

            // null hanya kalau mapel ini memang belum punya tugas sama sekali
            $rataTugas = $jumlahTugas > 0
                ? (float) ($rt->total_nilai ?? 0) / $jumlahTugas
                : null;

            $nilaiTugas = $rataTugas ?? 0;
            $nilaiSts = $uj->nilai_sts ?? 0;
            $nilaiSas = $uj->nilai_sas ?? 0;
            $predikatSikap = $sk->predikat ?? 'Baik';
            $sikapNumerik = self::PREDIKAT_NUMERIK[$predikatSikap] ?? 0;

            $nilaiAkhir = (
                $nilaiTugas * $bobot->bobot_tugas
                + $nilaiSts * $bobot->bobot_sts
                + $nilaiSas * $bobot->bobot_sas
                + $sikapNumerik * $bobot->bobot_sikap
            ) / 100;

            // Lengkap: semua tugas sudah dinilai + STS, SAS, sikap terisi
            $lengkap = $jumlahTugas > 0
                && $jumlahDinilai >= $jumlahTugas
                && $uj !== null && $uj->nilai_sts !== null && $uj->nilai_sas !== null
                && $sk !== null;

            $hasil->put($siswa->id, (object) [
                'siswa' => $siswa,
                'rata_tugas' => $rataTugas,
                'jumlah_tugas' => $jumlahTugas,
                'jumlah_tugas_dinilai' => $jumlahDinilai,
                'nilai_sts' => $uj->nilai_sts ?? null,
                'nilai_sas' => $uj->nilai_sas ?? null,
                'sikap_predikat' => $sk->predikat ?? null,
                'sikap_catatan' => $sk->catatan ?? null,
                'nilai_akhir' => round($nilaiAkhir, 1),
                'lengkap' => $lengkap,
            ]);
        }

        return ['bobot' => $bobot, 'siswa' => $hasil];
    }
}