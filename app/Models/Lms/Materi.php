<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_path',
        'urutan',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }
}
