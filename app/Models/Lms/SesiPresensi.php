<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Sesi presensi barcode: dibuka oleh guru pengampu di awal pertemuan.
 * Token unik ditampilkan sebagai QR code (di layar/proyektor), siswa
 * scan lewat akun LMS mereka sendiri untuk tercatat "Hadir" otomatis.
 * Guru menutup sesi setelah waktu tertentu; siswa yang belum sempat
 * scan diinput manual oleh guru (lihat PresensiLms.sumber = 'manual').
 */
class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'token',
        'tanggal',
        'dibuka_at',
        'ditutup_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dibuka_at' => 'datetime',
        'ditutup_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $sesi) {
            $sesi->token = $sesi->token ?: (string) Str::uuid();
            $sesi->dibuka_at = $sesi->dibuka_at ?: now();
        });
    }

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function dibukaOleh()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function presensi()
    {
        return $this->hasMany(PresensiLms::class, 'sesi_presensi_id');
    }

    public function getMasihAktifAttribute(): bool
    {
        return is_null($this->ditutup_at);
    }
}
