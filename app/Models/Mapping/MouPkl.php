<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

class MouPkl extends Model
{
    protected $table = 'mou_pkl';

    protected $fillable = [
        'tempat_pkl_id',
        'judul',
        'nomor_mou',
        'tanggal_mulai',
        'tanggal_berakhir',
        'keterangan',
        'file_path',
        'file_nama_asli',
        'file_ukuran',
        'is_public',
        'uploaded_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'is_public' => 'boolean',
    ];

    public function tempatPkl()
    {
        return $this->belongsTo(TempatPkl::class);
    }

    /** 'aktif' | 'berakhir' | 'tanpa_batas' */
    public function getStatusAttribute(): string
    {
        if (! $this->tanggal_berakhir) {
            return 'tanpa_batas';
        }

        return $this->tanggal_berakhir->copy()->endOfDay()->isPast() ? 'berakhir' : 'aktif';
    }

    public function getUkuranLabelAttribute(): string
    {
        $b = (int) $this->file_ukuran;

        if ($b >= 1048576) {
            return number_format($b / 1048576, 1) . ' MB';
        }

        return max(1, (int) round($b / 1024)) . ' KB';
    }
}