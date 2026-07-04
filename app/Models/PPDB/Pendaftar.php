<?php

namespace App\Models\PPDB;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $no_pendaftaran
 * @property string $nama_lengkap
 * @property string $jenis_kelamin
 * @property string $alamat
 * @property string $agama
 * @property string $nama_orang_tua
 * @property string $asal_sekolah
 * @property string $no_hp
 * @property int $jurusan_id
 * @property string $status
 * @property numeric $nominal_pembayaran
 * @property string|null $catatan_admin
 * @property int|null $processed_by
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $jenis_kelamin_label
 * @property-read string $no_hp_formatted
 * @property-read string $nominal_formatted
 * @property-read string $status_badge
 * @property-read string $status_label
 * @property-read string $whatsapp_url
 * @property-read \App\Models\PPDB\Jurusan $jurusan
 * @property-read User|null $processor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar byJurusan($jurusanId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar diterima()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAgama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAsalSekolah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCatatanAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereJurusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNamaOrangTua($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNoPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNominalPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pendaftar extends Model
{
    protected $table = 'pendaftar';

    protected $fillable = [
        'no_pendaftaran',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'agama',
        'nama_orang_tua',
        'asal_sekolah',
        'no_hp',
        'jurusan_id',
        'status',
        'nominal_pembayaran',
        'catatan_admin',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'nominal_pembayaran' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diterima' => 'Diterima',
            'ditolak'  => 'Ditolak',
            default    => 'Menunggu',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diterima' => 'badge-success',
            'ditolak'  => 'badge-danger',
            default    => 'badge-warning',
        };
    }

    public function getNoHpFormattedAttribute(): string
    {
        $no = preg_replace('/[^0-9]/', '', $this->no_hp);
        if (str_starts_with($no, '0')) {
            $no = '62' . substr($no, 1);
        }
        return $no;
    }

    public function getWhatsappUrlAttribute(): string
    {
        return 'https://wa.me/' . $this->no_hp_formatted;
    }

    public function getNominalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_pembayaran ?? 0, 0, ',', '.');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByJurusan($query, $jurusanId)
    {
        return $query->where('jurusan_id', $jurusanId);
    }

    // ── Static: Generate No Pendaftaran ───────────────────────
    public static function generateNoPendaftaran(): string
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'PPDB-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ── Business Logic ────────────────────────────────────────
    public function isLayakDiterima(): bool
    {
        return ($this->nominal_pembayaran ?? 0) >= 2500000;
    }
}
