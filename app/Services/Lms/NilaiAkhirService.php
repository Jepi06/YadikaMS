<?php

namespace App\Services\Lms;

use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PengaturanNilai;
use Illuminate\Support\Facades\DB;

/**
 * Satu tempat buat hitung Nilai Akhir, dipakai oleh:
 * - Lms\Guru\NilaiController (rekap 1 mapel, guru pengampu)
 * - Lms\Guru\WaliKelasController (rekap lintas mapel, buat wali kelas)
 * supaya rumusnya konsisten di mana pun dipakai.
 *
 * Komponen nilai:
 *  - Tugas (rata-rata nilai tugas yang sudah dinilai)
 *  - Praktik (rata-rata nilai praktik yang terisi; boleh banyak kali praktik)
 *      mode "gabung"   : praktik digabung ke tugas (rata-rata gabungan = total nilai / total item),
 *                        memakai persentase Tugas.
 *      mode "terpisah" : praktik punya persentase sendiri (bobot_praktik).
 *  - STS, SAS
 *  - Sikap/Keaktifan
 *      mode "utama"    : ikut dalam total 100%.
 *      mode "tambahan" : bonus poin di atas 100% komponen lain, maksimal sikap_maks_tambahan
 *                        poin; nilai akhir dibatasi maksimal 100.
 *    Nilai sikap bisa dari predikat (dropdown) atau custom (angka manual).
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
     * Return array:
     * [
     *   'bobot' => BobotNilai,
     *   'pengaturan' => PengaturanNilai,
     *   'jumlah_praktik' => int (jumlah kegiatan praktik yang dibuat guru),
     *   'siswa' => Collection keyed by siswa_id, tiap item stdClass:
     *              siswa, rata_tugas, jumlah_tugas_dinilai,
     *              rata_praktik, jumlah_praktik_dinilai, rata_gabungan,
     *              nilai_sts, nilai_sas, sikap_predikat, sikap_catatan,
     *              sikap_custom (bool), sikap_nilai (angka dipakai), bonus_sikap,
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

        $pengaturan = PengaturanNilai::firstOrCreate(
            ['pengampu_mapel_id' => $pengampuMapel->id],
            ['praktik_gabung_tugas' => true, 'bobot_praktik' => 0, 'sikap_mode' => 'utama', 'sikap_maks_tambahan' => 10]
        );

        $gabung = (bool) $pengaturan->praktik_gabung_tugas;
        $sikapTambahan = $pengaturan->sikap_mode === 'tambahan';
        $maksTambahan = (float) $pengaturan->sikap_maks_tambahan;

        $wTugas = (float) $bobot->bobot_tugas;
        $wPraktik = $gabung ? 0.0 : (float) $pengaturan->bobot_praktik;
        $wSts = (float) $bobot->bobot_sts;
        $wSas = (float) $bobot->bobot_sas;
        $wSikap = $sikapTambahan ? 0.0 : (float) $bobot->bobot_sikap;

        $rataRataTugas = DB::table('pengumpulan_tugas')
            ->join('tugas', 'tugas.id', '=', 'pengumpulan_tugas.tugas_id')
            ->where('tugas.pengampu_mapel_id', $pengampuMapel->id)
            ->whereNotNull('pengumpulan_tugas.dinilai_at')
            ->groupBy('pengumpulan_tugas.siswa_id')
            ->select(
                'pengumpulan_tugas.siswa_id',
                DB::raw('AVG(pengumpulan_tugas.nilai) as rata_rata'),
                DB::raw('SUM(pengumpulan_tugas.nilai) as total'),
                DB::raw('COUNT(*) as jumlah_dinilai')
            )
            ->get()
            ->keyBy('siswa_id');

        $jumlahPraktik = DB::table('praktik_lms')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->count();

        $rataRataPraktik = DB::table('nilai_praktik_lms')
            ->join('praktik_lms', 'praktik_lms.id', '=', 'nilai_praktik_lms.praktik_id')
            ->where('praktik_lms.pengampu_mapel_id', $pengampuMapel->id)
            ->whereNotNull('nilai_praktik_lms.nilai')
            ->groupBy('nilai_praktik_lms.siswa_id')
            ->select(
                'nilai_praktik_lms.siswa_id',
                DB::raw('AVG(nilai_praktik_lms.nilai) as rata_rata'),
                DB::raw('SUM(nilai_praktik_lms.nilai) as total'),
                DB::raw('COUNT(*) as jumlah_dinilai')
            )
            ->get()
            ->keyBy('siswa_id');

        $nilaiUjian = NilaiUjian::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');
        $nilaiSikap = NilaiSikap::where('pengampu_mapel_id', $pengampuMapel->id)->get()->keyBy('siswa_id');

        $hasil = collect();

        foreach ($pengampuMapel->kelas->siswa as $siswa) {
            $rt = $rataRataTugas->get($siswa->id);
            $rp = $rataRataPraktik->get($siswa->id);
            $uj = $nilaiUjian->get($siswa->id);
            $sk = $nilaiSikap->get($siswa->id);

            // Rata-rata gabungan tugas + praktik = (total nilai) / (jumlah item)
            $cnt = (int) ($rt->jumlah_dinilai ?? 0) + (int) ($rp->jumlah_dinilai ?? 0);
            $tot = (float) ($rt->total ?? 0) + (float) ($rp->total ?? 0);
            $rataGabungan = $cnt > 0 ? $tot / $cnt : null;

            if ($gabung) {
                $nilaiTugas = $rataGabungan ?? 0;
                $nilaiPraktik = 0;
            } else {
                $nilaiTugas = $rt->rata_rata ?? 0;
                $nilaiPraktik = $rp->rata_rata ?? 0;
            }

            $nilaiSts = $uj->nilai_sts ?? 0;
            $nilaiSas = $uj->nilai_sas ?? 0;

            $predikatSikap = $sk->predikat ?? 'Baik';
            $sikapCustom = $sk !== null && (bool) ($sk->is_custom ?? false) && ($sk->nilai_custom ?? null) !== null;
            $sikapNumerik = $sikapCustom
                ? (float) $sk->nilai_custom
                : (float) (self::PREDIKAT_NUMERIK[$predikatSikap] ?? 0);

            $dasar = (
                $nilaiTugas * $wTugas
                + $nilaiPraktik * $wPraktik
                + $nilaiSts * $wSts
                + $nilaiSas * $wSas
                + $sikapNumerik * $wSikap
            ) / 100;

            $bonusSikap = $sikapTambahan ? ($sikapNumerik / 100) * $maksTambahan : 0;
            $nilaiAkhir = min(100, $dasar + $bonusSikap);

            $lengkap = $rt !== null
                && ($gabung || $wPraktik <= 0 || $rp !== null)
                && $uj !== null && $uj->nilai_sts !== null && $uj->nilai_sas !== null
                && $sk !== null;

            $hasil->put($siswa->id, (object) [
                'siswa' => $siswa,
                'rata_tugas' => $rt->rata_rata ?? null,
                'jumlah_tugas_dinilai' => $rt->jumlah_dinilai ?? 0,
                'rata_praktik' => $rp->rata_rata ?? null,
                'jumlah_praktik_dinilai' => $rp->jumlah_dinilai ?? 0,
                'rata_gabungan' => $rataGabungan,
                'nilai_sts' => $uj->nilai_sts ?? null,
                'nilai_sas' => $uj->nilai_sas ?? null,
                'sikap_predikat' => $sk->predikat ?? null,
                'sikap_catatan' => $sk->catatan ?? null,
                'sikap_custom' => $sikapCustom,
                'sikap_nilai' => $sikapNumerik,
                'bonus_sikap' => round($bonusSikap, 1),
                'nilai_akhir' => round($nilaiAkhir, 1),
                'lengkap' => $lengkap,
            ]);
        }

        return [
            'bobot' => $bobot,
            'pengaturan' => $pengaturan,
            'jumlah_praktik' => $jumlahPraktik,
            'siswa' => $hasil,
        ];
    }
}
