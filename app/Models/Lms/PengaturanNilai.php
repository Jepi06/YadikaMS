<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class PengaturanNilai extends Model
{
    protected $table = 'pengaturan_nilai_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'praktik_gabung_tugas',
        'bobot_praktik',
        'sikap_mode',
        'sikap_maks_tambahan',
    ];

    protected $casts = [
        'praktik_gabung_tugas' => 'boolean',
    ];
}
