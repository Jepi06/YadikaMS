<?php

namespace App\Models\Mapping;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_kelas
 * @property string $tingkat
 * @property int $jurusan_id
 * @property int|null $wali_kelas_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mapping\Jurusan $jurusan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\Siswa> $siswa
 * @property-read int|null $siswa_count
 * @property-read User|null $waliKelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereJurusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereNamaKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereTingkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereWaliKelasId($value)
 * @mixin \Eloquent
 */
class Kelas extends Model
{
    protected $table    = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'jurusan_id', 'wali_kelas_id'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
