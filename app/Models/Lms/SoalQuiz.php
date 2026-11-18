<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class SoalQuiz extends Model
{
    protected $table = 'soal_quiz_lms';

    protected $fillable = [
        'quiz_id', 'urutan', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e',
        'kunci', 'poin', 'pembahasan',
    ];

    protected $casts = ['poin' => 'float'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /** ['A' => teks, 'B' => teks, ...] hanya opsi yang terisi. */
    public function opsi(): array
    {
        return array_filter([
            'A' => $this->opsi_a, 'B' => $this->opsi_b, 'C' => $this->opsi_c,
            'D' => $this->opsi_d, 'E' => $this->opsi_e,
        ], fn($v) => $v !== null && $v !== '');
    }
}
