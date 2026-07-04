<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $deskripsi
 * @property int $kuota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\Kelas> $kelas
 * @property-read int|null $kelas_count
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
    protected $fillable = ['nama', 'kode'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
