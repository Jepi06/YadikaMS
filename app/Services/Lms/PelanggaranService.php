<?php

namespace App\Services\Lms;

use App\Models\Lms\PelanggaranSiswa;
use App\Models\Siswa;

class PelanggaranService
{
    public const DEPOSIT = 500;   // poin awal setiap siswa; saldo 0 = batas maksimal

    /**
     * Pedoman penerapan sanksi (Bab IV-e). Setiap siswa punya deposit 500 poin; $terpakai =
     * pengurangan bersih (pelanggaran − prestasi), dihitung dari seluruh riwayat selama menjadi siswa.
     * Catatan: pedoman hanya menyebut 301-400 dan 500; rentang 401-499 diperlakukan
     * sama dengan tahap 301-400 sampai mencapai 500.
     */
    public static function tingkat(int $terpakai): array
    {
        $terpakai = max(0, $terpakai);

        return match (true) {
            $terpakai >= 500 => [
                'kode' => 6, 'label' => 'Dikembalikan ke orang tua / dikeluarkan',
                'tindakan' => 'Siswa dikembalikan ke orang tua atau dikeluarkan dari sekolah (oleh Kepala Sekolah).',
                'warna' => 'rose', 'bg' => 'bg-rose-50', 'teks' => 'text-rose-700', 'bar' => 'bg-rose-600',
            ],
            $terpakai >= 301 => [
                'kode' => 5, 'label' => 'Panggilan orang tua ke-3',
                'tindakan' => 'Panggilan ke-3 kepada orang tua dengan membuat perjanjian siswa yang bersangkutan diketahui orang tua dan pihak sekolah.',
                'warna' => 'rose', 'bg' => 'bg-rose-50', 'teks' => 'text-rose-700', 'bar' => 'bg-rose-500',
            ],
            $terpakai >= 201 => [
                'kode' => 4, 'label' => 'Panggilan orang tua ke-2',
                'tindakan' => 'Panggilan ke-2 kepada orang tua siswa yang bersangkutan (oleh Wali Kelas/Guru BK/Kepala Sekolah).',
                'warna' => 'orange', 'bg' => 'bg-orange-50', 'teks' => 'text-orange-700', 'bar' => 'bg-orange-500',
            ],
            $terpakai >= 101 => [
                'kode' => 3, 'label' => 'Panggilan orang tua ke-1',
                'tindakan' => 'Panggilan ke-1 kepada orang tua siswa yang bersangkutan (oleh Wali Kelas/Guru BK/Kepala Sekolah).',
                'warna' => 'amber', 'bg' => 'bg-amber-50', 'teks' => 'text-amber-700', 'bar' => 'bg-amber-500',
            ],
            $terpakai >= 51 => [
                'kode' => 2, 'label' => 'Peringatan ke-2',
                'tindakan' => 'Peringatan ke-2 lisan oleh Wali Kelas.',
                'warna' => 'yellow', 'bg' => 'bg-yellow-50', 'teks' => 'text-yellow-700', 'bar' => 'bg-yellow-500',
            ],
            $terpakai >= 20 => [
                'kode' => 1, 'label' => 'Peringatan ke-1',
                'tindakan' => 'Peringatan ke-1 lisan oleh Wali Kelas.',
                'warna' => 'sky', 'bg' => 'bg-sky-50', 'teks' => 'text-sky-700', 'bar' => 'bg-sky-500',
            ],
            default => [
                'kode' => 0, 'label' => 'Aman',
                'tindakan' => 'Belum ada tindakan. Pertahankan kedisiplinanmu!',
                'warna' => 'emerald', 'bg' => 'bg-emerald-50', 'teks' => 'text-emerald-700', 'bar' => 'bg-emerald-500',
            ],
        };
    }

    /** Poin terpakai = pelanggaran − prestasi (negatif bila prestasi lebih banyak → saldo > 500). */
    private static function terpakaiSql(): string
    {
        return "SUM(CASE WHEN tipe = 'prestasi' THEN -poin ELSE poin END)";
    }

    /** [siswa_id => poin terpakai] */
    public static function terpakaiMassal(array $siswaIds): array
    {
        return PelanggaranSiswa::whereIn('siswa_id', $siswaIds)
            ->groupBy('siswa_id')
            ->selectRaw('siswa_id, ' . self::terpakaiSql() . ' as t')
            ->pluck('t', 'siswa_id')
            ->map(fn($v) => (int) $v)   // boleh negatif: prestasi > pelanggaran
            ->all();
    }

    /** [siswa_id => saldo poin]; deposit 500, tanpa batas atas (prestasi bisa membuat > 500). */
    public static function saldoMassal(array $siswaIds): array
    {
        $terpakai = self::terpakaiMassal($siswaIds);
        $hasil = [];
        foreach ($siswaIds as $id) {
            $hasil[$id] = self::DEPOSIT - ($terpakai[$id] ?? 0);
        }

        return $hasil;
    }

    public static function ringkasan(Siswa $siswa, int $limit = 5): array
    {
        $q = PelanggaranSiswa::where('siswa_id', $siswa->id);
        $pelanggaran = (int) (clone $q)->where('tipe', 'pelanggaran')->sum('poin');
        $prestasi    = (int) (clone $q)->where('tipe', 'prestasi')->sum('poin');
        $terpakai    = $pelanggaran - $prestasi;   // boleh negatif
        $saldo       = self::DEPOSIT - $terpakai;

        return [
            'deposit'     => self::DEPOSIT,
            'saldo'       => $saldo,
            'pelanggaran' => $pelanggaran,
            'prestasi'    => $prestasi,
            'terpakai'    => $terpakai,
            'persen'      => max(0, min(100, round($saldo / self::DEPOSIT * 100, 1))),   // bar maks 100%, saldo tetap bisa > 500
            'tingkat'     => self::tingkat(max(0, $terpakai)),
            'terbaru'     => $limit > 0
                ? PelanggaranSiswa::where('siswa_id', $siswa->id)->orderByDesc('tanggal')->orderByDesc('id')->limit($limit)->get()
                : collect(),
        ];
    }
}
