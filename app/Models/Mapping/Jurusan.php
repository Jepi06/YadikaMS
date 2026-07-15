<?php

namespace App\Models\Mapping;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';
    protected $fillable = ['nama', 'kode', 'kepala_jurusan_id'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    /** BARU: dipakai untuk scoping approval kepala_jurusan */
    public function kepalaJurusan()
    {
        return $this->belongsTo(User::class, 'kepala_jurusan_id');
    }
}
