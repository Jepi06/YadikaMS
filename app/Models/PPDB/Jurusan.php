<?php

namespace App\Models\PPDB;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PPDB\Pendaftar> $pendaftar
 * @property-read int|null $pendaftar_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PPDB\Pendaftar> $pendaftarDiterima
 * @property-read int|null $pendaftar_diterima_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereUpdatedAt($value)
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
        return $this->hasMany(Pendaftar::class, 'jurusan_id')->where('status', 'diterima');
    }

    public function getTotalDiterimaAttribute(): int
    {
        return $this->pendaftar()->where('status', 'diterima')->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return $this->kuota - $this->total_diterima;
    }
}
