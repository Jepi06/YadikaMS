<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

class PengaturanPkl extends Model
{
    protected $table = 'pengaturan_pkl';

    protected $fillable = ['kunci', 'nilai'];

    public const KUNCI_PENGAJUAN_DIBUKA = 'pengajuan_dibuka';

    /** Notifikasi di LMS hanya untuk siswa tingkat ini. */
    public const TINGKAT_TARGET = 12;

    /** Apakah admin PKL sedang membuka pengajuan? Default: tertutup. */
    public static function pengajuanDibuka(): bool
    {
        try {
            return static::where('kunci', self::KUNCI_PENGAJUAN_DIBUKA)->value('nilai') === '1';
        } catch (\Throwable $e) {
            return false; // tabel belum dimigrasi → anggap tertutup
        }
    }

    public static function setPengajuanDibuka(bool $dibuka): void
    {
        static::updateOrCreate(
            ['kunci' => self::KUNCI_PENGAJUAN_DIBUKA],
            ['nilai' => $dibuka ? '1' : '0']
        );
    }

    /**
     * Apakah kelas ini termasuk target notifikasi (kelas XII)?
     * Cek kolom tingkat, dan nama kelas yang diawali "XII" sebagai cadangan.
     */
    public static function kelasTarget($kelas): bool
    {
        if (! $kelas) {
            return false;
        }

        if ((int) ($kelas->tingkat ?? 0) === self::TINGKAT_TARGET) {
            return true;
        }

        $nama = strtoupper(trim((string) ($kelas->nama_kelas ?? '')));

        return str_starts_with($nama, 'XII');
    }
}