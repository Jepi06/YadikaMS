<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_lampiran',
        'batas_waktu',
    ];

    protected $casts = [
        'batas_waktu' => 'datetime',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function getSudahLewatBatasWaktuAttribute(): bool
    {
        return $this->batas_waktu?->isPast() ?? false;
    }
}
