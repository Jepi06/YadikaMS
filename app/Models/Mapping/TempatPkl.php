<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_tempat
 * @property string $alamat
 * @property string|null $bidang_usaha
 * @property string|null $nama_kontak
 * @property string|null $no_telp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereBidangUsaha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNamaKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNamaTempat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNoTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TempatPkl extends Model
{
    protected $table    = 'tempat_pkl';
    protected $fillable = ['nama_tempat', 'alamat', 'bidang_usaha', 'nama_kontak', 'no_telp'];

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }
}
