<?php

namespace App\Models;

use App\Models\Mapping\Siswa;
use App\Models\SPMB\Jurusan;
use App\Models\Lms\PengampuMapel;
use Illuminate\Database\Eloquent\Model;

/**
 * CATATAN: buat file ini HANYA kalau model Kelas belum ada di project.
 * Berdasarkan migration create_kelas_table, tabel ini dipakai bersama
 * oleh PKL (siswa.kelas_id), SPMB, dan LMS — jadi taruh di App\Models
 * (bukan di namespace modul tertentu) supaya bisa dipakai lintas modul.
 * Kalau kamu sudah punya model Kelas di namespace lain, sesuaikan
 * `use App\Models\Kelas;` di model-model LMS lain menjadi namespace itu.
 */
class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan_id',
        'wali_kelas_id',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function pengampuMapel()
    {
        return $this->hasMany(PengampuMapel::class, 'kelas_id');
    }
}
