<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class NilaiPraktik extends Model
{
    protected $table = 'nilai_praktik_lms';

    protected $fillable = ['praktik_id', 'siswa_id', 'nilai', 'catatan'];

    public function praktik()
    {
        return $this->belongsTo(Praktik::class, 'praktik_id');
    }
}
