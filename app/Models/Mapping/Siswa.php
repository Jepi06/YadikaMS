<?php

namespace App\Models\Mapping;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nis
 * @property string $nama
 * @property string $jenis_kelamin
 * @property string|null $alamat
 * @property string|null $no_hp
 * @property int $kelas_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mapping\Kelas $kelas
 * @property-read \App\Models\Mapping\PenempatanPkl|null $penempatanAktif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa belumMengajukan()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa sudahMengajukan()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Siswa extends Model
{
    protected $table    = 'siswa';
    protected $fillable = ['nis', 'nama', 'jenis_kelamin', 'alamat', 'no_hp', 'kelas_id', 'user_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Akun login siswa (jika sudah dihubungkan). Nullable — siswa yang
     * belum punya akun tetap bisa terdaftar di tabel ini oleh admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }

    /**
     * Penempatan yang masih berjalan/aktif (bukan yang ditolak),
     * dipakai untuk menampilkan status pengajuan PKL siswa saat ini.
     */
    public function penempatanAktif()
    {
        return $this->hasOne(PenempatanPkl::class)->whereNotIn('status', ['rejected'])->latest();
    }

    public function sudahDitempatkan(): bool
    {
        return $this->penempatanPkl()
            ->whereNotIn('status', ['rejected'])
            ->exists();
    }

    /**
     * Scope: siswa yang BELUM pernah mengajukan PKL sama sekali,
     * atau seluruh pengajuannya ditolak (jadi status efektifnya "belum mengajukan").
     * Dipakai admin untuk memantau siapa yang belum jalan proses PKL.
     */
    public function scopeBelumMengajukan($query)
    {
        return $query->whereDoesntHave('penempatanPkl', function ($q) {
            $q->whereNotIn('status', ['rejected']);
        });
    }

    /**
     * Scope: siswa yang sudah punya pengajuan aktif (draft/diajukan/approved).
     */
    public function scopeSudahMengajukan($query)
    {
        return $query->whereHas('penempatanPkl', function ($q) {
            $q->whereNotIn('status', ['rejected']);
        });
    }
}
