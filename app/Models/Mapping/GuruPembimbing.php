<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $nip
 * @property string $nama
 * @property string|null $no_hp
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuruPembimbing extends Model
{
    protected $table    = 'guru_pembimbing';
    protected $fillable = ['nip', 'nama', 'no_hp', 'email'];

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }
}
