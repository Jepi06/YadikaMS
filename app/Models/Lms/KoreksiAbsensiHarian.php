<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class KoreksiAbsensiHarian extends Model
{
    protected $table = 'koreksi_absensi_harian_lms';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];
}