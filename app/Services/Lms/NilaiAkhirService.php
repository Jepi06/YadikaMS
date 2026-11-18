<?php

namespace App\Services\Lms;

use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\Praktik;
use App\Services\Lms\QuizService;
use Illuminate\Support\Facades\DB;

/**
 * Satu tempat buat hitung Nilai Akhir, dipakai oleh:
 * - Lms\Guru\NilaiController (rekap 1 mapel, guru pengampu)
 * - Lms\Guru\WaliKelasController (rekap lintas mapel, buat wali kelas)
 *
 * Opsi per mapel (tabel bobot_nilai_lms):
 *  praktik_mode : 'gabung'   → praktik dirata-rata BERSAMA tugas (tiap tugas & tiap praktik bobotnya sama)
 *                 'terpisah' → praktik punya bobot sendiri (bobot_praktik)
 *  sikap_mode   : 'include'  → sikap/keaktifan ikut dalam 100% (bobot_sikap)
 *                 'tambahan' → sikap TIDAK masuk 100%; jadi poin tambahan:
 *                              bonus = min(sikap_bonus_maks, nilai_keaktifan × sikap_bonus_persen / 100)
 *                              nilai akhir dibatasi maksimal 100.
 *  quiz_mode    : 'nonaktif' → quiz tidak dihitung (default)
 *                 'include'  → rata-rata nilai quiz ikut dalam 100% (bobot_quiz)
 *                 'tambahan' → poin tambahan di luar 100%:
 *                              bonus = min(quiz_bonus_maks, rata_quiz × quiz_bonus_persen / 100)
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

    /** Bobot yang benar-benar dipakai menurut mode (komponen non-aktif = 0). */
    public static function bobotEfektif(BobotNilai $bobot): array
    {
        $terpisah = ($bobot->praktik_mode ?? 'gabung') === 'terpisah';
        $include  = ($bobot->sikap_mode ?? 'include') === 'include';
        $quizMode = $bobot->quiz_mode ?? 'nonaktif';

        return [
            'praktik_terpisah' => $terpisah,
            'sikap_include'    => $include,
            'tugas'            => (int) $bobot->bobot_tugas,
            'praktik'          => $terpisah ? (int) ($bobot->bobot_praktik ?? 0) : 0,
            'sts'              => (int) $bobot->bobot_sts,
            'sas'              => (int) $bobot->bobot_sas,
            'sikap'            => $include ? (int) $bobot->bobot_sikap : 0,
            'bonus_persen'     => (int) ($bobot->sikap_bonus_persen ?? 10),
            'bonus_maks'       => (int) ($bobot->sikap_bonus_maks ?? 5),
            'quiz_mode'        => $quizMode,
            'quiz_include'     => $quizMode === 'include',
            'quiz_tambahan'    => $quizMode === 'tambahan',
            'quiz'             => $quizMode === 'include' ? (int) ($bobot->bobot_quiz ?? 0) : 0,
            'quiz_bonus_persen' => (int) ($bobot->quiz_bonus_persen ?? 10),
            'quiz_bonus_maks'  => (int) ($bobot->quiz_bonus_maks ?? 5),
        ];
    }

    /**
     * Return:
     * [
     *   'bobot'         => BobotNilai,
     *   'bobot_efektif' => array (lihat bobotEfektif),
     *   'ada_praktik'   => int jumlah pertemuan praktik,
     *   'siswa'         => Collection keyed by siswa_id, tiap item stdClass:
     *       siswa, rata_tugas, rata_praktik, rata_komponen_tugas,
     *       jumlah_tugas_dinilai, jumlah_praktik_dinilai, nilai_sts, nilai_sas,
     *       sikap_predikat, sikap_catatan, sikap_numerik, bonus_sikap,
     *       nilai_dasar, nilai_akhir, lengkap
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
        if ($bobot->wasRecentlyCreated) {
            $bobot->refresh(); // muat nilai default kolom baru dari database
        }
        $w = self::bobotEfektif($bobot);

        // Jumlah & total nilai tugas per siswa
        $aggTugas = DB::table('pengumpulan_tugas')
            ->join('tugas', 'tugas.id', '=', 'pengumpulan_tugas.tugas_id')
            ->where('tugas.pengampu_mapel_id', $pengampuMapel->id)
            ->whereNotNull('pengumpulan_tugas.dinilai_at')
            ->whereNotNull('pengumpulan_tugas.nilai')
            ->groupBy('pengumpulan_tugas.siswa_id')
            ->select('pengumpulan_tugas.siswa_id', DB::raw('SUM(pengumpulan_tugas.nilai) as total'), DB::raw('COUNT(*) as jumlah'))
            ->get()->keyBy('siswa_id');

        // Jumlah & total nilai praktik per siswa
        $aggPraktik = DB::table('nilai_praktik_lms')
            ->join('praktik_lms', 'praktik_lms.id', '=', 'nilai_praktik_lms.praktik_id')
            ->where('praktik_lms.pengampu_mapel_id', $pengampuMapel->id)
            ->whereNotNull('nilai_praktik_lms.nilai')
            ->groupBy('nilai_praktik_lms.siswa_id')
            ->select('nilai_praktik_lms.siswa_id', DB::raw('SUM(nilai_praktik_lms.nilai) as total'), DB::raw('COUNT(*) as jumlah'))
            ->get()->keyBy('siswa_id');

        $quizPerSiswa = $w['quiz_mode'] !== 'nonaktif' ? QuizService::rataPerSiswa($pengampuMapel) : [];

        $nilaiUjian = NilaiUjian::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');
        $nilaiSikap = NilaiSikap::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');

        $hasil = collect();

        foreach ($pengampuMapel->kelas->siswa as $siswa) {
            $tg = $aggTugas->get($siswa->id);
            $pr = $aggPraktik->get($siswa->id);
            $uj = $nilaiUjian->get($siswa->id);
            $sk = $nilaiSikap->get($siswa->id);

            $tTotal = (float) ($tg->total ?? 0);
            $tN     = (int) ($tg->jumlah ?? 0);
            $pTotal = (float) ($pr->total ?? 0);
            $pN     = (int) ($pr->jumlah ?? 0);

            $rataTugas   = $tN > 0 ? $tTotal / $tN : null;
            $rataPraktik = $pN > 0 ? $pTotal / $pN : null;

            if ($w['praktik_terpisah']) {
                $komponenTugas   = $rataTugas;
                $komponenPraktik = $rataPraktik;
            } else {
                $n = $tN + $pN;                                   // praktik digabung ke tugas
                $komponenTugas   = $n > 0 ? ($tTotal + $pTotal) / $n : null;
                $komponenPraktik = null;
            }

            $rq = $quizPerSiswa[$siswa->id] ?? ['rata' => null, 'jumlah' => 0];

            $sikapNumerik = $sk ? (self::PREDIKAT_NUMERIK[$sk->predikat] ?? 0) : null;

            $nilaiDasar = (
                ($komponenTugas ?? 0) * $w['tugas']
                + ($komponenPraktik ?? 0) * $w['praktik']
                + ($uj->nilai_sts ?? 0) * $w['sts']
                + ($uj->nilai_sas ?? 0) * $w['sas']
                // mode include: tanpa catatan sikap dianggap 'Baik' (perilaku lama)
                + ($w['sikap_include'] ? ($sikapNumerik ?? self::PREDIKAT_NUMERIK['Baik']) * $w['sikap'] : 0)
                + ($w['quiz_include'] ? ($rq['rata'] ?? 0) * $w['quiz'] : 0)
            ) / 100;

            $bonus = 0.0;
            if (! $w['sikap_include'] && $sikapNumerik !== null) {
                $bonus = min($w['bonus_maks'], $sikapNumerik * $w['bonus_persen'] / 100);
            }

            $bonusQuiz = 0.0;
            if ($w['quiz_tambahan'] && $rq['rata'] !== null) {
                $bonusQuiz = min($w['quiz_bonus_maks'], $rq['rata'] * $w['quiz_bonus_persen'] / 100);
            }

            $nilaiAkhir = min(100, $nilaiDasar + $bonus + $bonusQuiz);

            $adaTugas = $w['praktik_terpisah'] ? $tN > 0 : ($tN + $pN) > 0;
            $lengkap = $adaTugas
                && (! $w['praktik_terpisah'] || $w['praktik'] === 0 || $pN > 0)
                && $uj !== null && $uj->nilai_sts !== null && $uj->nilai_sas !== null
                && ($w['sikap_include'] ? $sk !== null : true)
                && (! $w['quiz_include'] || $rq['rata'] !== null);

            $hasil->put($siswa->id, (object) [
                'siswa' => $siswa,
                'rata_tugas' => $rataTugas,
                'rata_praktik' => $rataPraktik,
                'rata_komponen_tugas' => $komponenTugas,
                'jumlah_tugas_dinilai' => $tN,
                'jumlah_praktik_dinilai' => $pN,
                'nilai_sts' => $uj->nilai_sts ?? null,
                'nilai_sas' => $uj->nilai_sas ?? null,
                'sikap_predikat' => $sk->predikat ?? null,
                'sikap_catatan' => $sk->catatan ?? null,
                'sikap_numerik' => $sikapNumerik,
                'bonus_sikap' => round($bonus, 1),
                'rata_quiz' => $rq['rata'],
                'jumlah_quiz' => $rq['jumlah'],
                'bonus_quiz' => round($bonusQuiz, 1),
                'nilai_dasar' => round($nilaiDasar, 1),
                'nilai_akhir' => round($nilaiAkhir, 1),
                'lengkap' => $lengkap,
            ]);
        }

        return [
            'bobot' => $bobot,
            'bobot_efektif' => $w,
            'ada_praktik' => Praktik::where('pengampu_mapel_id', $pengampuMapel->id)->count(),
            'ada_quiz' => \App\Models\Lms\Quiz::where('pengampu_mapel_id', $pengampuMapel->id)->whereIn('status', ['terbit', 'ditutup'])->count(),
            'siswa' => $hasil,
        ];
    }
}
