<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class KoreksiAbsensiLog extends Model
{
    protected $table = 'koreksi_absensi_log_lms';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];
}