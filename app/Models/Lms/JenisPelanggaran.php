<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran_lms';

    protected $fillable = ['tipe', 'kategori', 'nama', 'poin', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean', 'poin' => 'integer'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
}
