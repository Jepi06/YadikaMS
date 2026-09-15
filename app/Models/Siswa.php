<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'kelas_id',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $q, ?string $keyword): Builder
    {
        if (!$keyword) return $q;

        return $q->where(function (Builder $sub) use ($keyword) {
            $sub->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nis', 'like', "%{$keyword}%")
                ->orWhere('no_hp', 'like', "%{$keyword}%");
        });
    }

    public function scopeFilterKelas(Builder $q, ?string $kelasId): Builder
    {
        return $kelasId ? $q->where('kelas_id', $kelasId) : $q;
    }

    public function scopeFilterJurusan(Builder $q, ?string $jurusanId): Builder
    {
        return $jurusanId
            ? $q->whereHas('kelas', fn($k) => $k->where('jurusan_id', $jurusanId))
            : $q;
    }

    public function scopeFilterTingkat(Builder $q, ?string $tingkat): Builder
    {
        return $tingkat
            ? $q->whereHas('kelas', fn($k) => $k->where('tingkat', $tingkat))
            : $q;
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
