<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz_lms';

    protected $fillable = [
        'pengampu_mapel_id', 'judul', 'deskripsi', 'durasi_menit', 'mulai_pada', 'selesai_pada',
        'acak_soal', 'maks_percobaan', 'tampil_pembahasan', 'status',
    ];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
        'acak_soal' => 'boolean',
        'tampil_pembahasan' => 'boolean',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function soal()
    {
        return $this->hasMany(SoalQuiz::class, 'quiz_id')->orderBy('urutan');
    }

    public function percobaan()
    {
        return $this->hasMany(PercobaanQuiz::class, 'quiz_id');
    }

    /** Boleh mulai dikerjakan sekarang? (terbit + dalam jendela waktu) */
    public function bisaDimulai(): bool
    {
        if ($this->status !== 'terbit') {
            return false;
        }
        if ($this->mulai_pada && now()->lt($this->mulai_pada)) {
            return false;
        }
        if ($this->selesai_pada && now()->gt($this->selesai_pada)) {
            return false;
        }

        return true;
    }

    /** Sudah berakhir? (ditutup guru atau lewat jendela waktu) */
    public function sudahBerakhir(): bool
    {
        return $this->status === 'ditutup' || ($this->selesai_pada && now()->gt($this->selesai_pada));
    }
}
