<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Praktik extends Model
{
    protected $table = 'praktik_lms';

    protected $fillable = ['pengampu_mapel_id', 'judul', 'tanggal', 'deskripsi'];

    protected $casts = ['tanggal' => 'date'];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class, 'pengampu_mapel_id');
    }

    public function nilai()
    {
        return $this->hasMany(NilaiPraktik::class, 'praktik_id');
    }
}
