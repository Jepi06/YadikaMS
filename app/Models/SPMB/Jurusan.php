<?php

namespace App\Models\SPMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $deskripsi
 * @property int $kuota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $sisa_kuota
 * @property-read int $total_diterima
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SPMB\Pendaftar> $pendaftar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SPMB\Pendaftar> $pendaftarDiterima
 * @mixin \Eloquent
 */
class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'kuota',
    ];

    public function pendaftar(): HasMany
    {
        return $this->hasMany(Pendaftar::class, 'jurusan_id');
    }

    public function pendaftarDiterima(): HasMany
    {
        return $this->hasMany(Pendaftar::class, 'jurusan_id')->where('status', Pendaftar::STATUS_DITERIMA);
    }

    public function getTotalDiterimaAttribute(): int
    {
        return $this->pendaftar()->where('status', Pendaftar::STATUS_DITERIMA)->count();
    }

    public function getTotalPendaftarAttribute(): int
    {
        return $this->pendaftar()->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->total_diterima);
    }
}
