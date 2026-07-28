<?php

namespace App\Models\Lms;

use App\Models\Mapping\Siswa;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'file_jawaban',
        'catatan_siswa',
        'nilai',
        'catatan_guru',
        'dikumpulkan_at',
        'dinilai_at',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dikumpulkan_at' => 'datetime',
        'dinilai_at' => 'datetime',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function getSudahDinilaiAttribute(): bool
    {
        return !is_null($this->dinilai_at);
    }
}
