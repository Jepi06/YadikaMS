<?php

namespace App\Models\Lms;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class PercobaanQuiz extends Model
{
    protected $table = 'percobaan_quiz_lms';

    protected $fillable = [
        'quiz_id', 'siswa_id', 'mulai_at', 'deadline_at', 'selesai_at', 'status', 'urutan_soal',
        'poin_didapat', 'poin_total', 'benar', 'total_soal', 'nilai',
    ];

    protected $casts = [
        'mulai_at' => 'datetime',
        'deadline_at' => 'datetime',
        'selesai_at' => 'datetime',
        'urutan_soal' => 'array',
        'nilai' => 'float',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanQuiz::class, 'percobaan_id');
    }
}
