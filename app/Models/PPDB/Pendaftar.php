<?php
// app/Models/Pendaftar.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pendaftar extends Model
{
    use HasFactory;

    protected $table = 'pendaftar';

    // Batas nominal pembayaran administrasi agar otomatis "diterima"
    const MINIMAL_DITERIMA = 2500000;

    const STATUS_PENDING = 'pending';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK = 'ditolak';

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
        'created_by',
    ];

    protected $casts = [
        'nominal_pembayaran' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke jurusan HANYA untuk kebutuhan tampilan (nama jurusan),
     * tanpa constraint FK di database. Jika data jurusan dihapus,
     * pendaftar tetap aman (nullOnDelete tidak dipasang di migration).
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ================= SCOPES ================= */

    public function scopeDiterima($query)
    {
        return $query->where('status', self::STATUS_DITERIMA);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopePerJurusan($query, $jurusanId)
    {
        return $query->where('jurusan_id', $jurusanId);
    }

    /* ================= HELPERS / BUSINESS LOGIC ================= */

    /**
     * Generate nomor pendaftaran otomatis: PPDB-2026-0001
     */
    public static function generateNoPendaftaran(): string
    {
        $tahun = now()->year;
        $last = self::where('no_pendaftaran', 'like', "PPDB-{$tahun}-%")
            ->orderByDesc('id')
            ->first();

        $urut = 1;
        if ($last) {
            $urut = (int) substr($last->no_pendaftaran, -4) + 1;
        }

        return sprintf('PPDB-%s-%04d', $tahun, $urut);
    }

    /**
     * Cek apakah nominal memenuhi syarat otomatis diterima
     */
    public function isEligibleForAutoAccept(): bool
    {
        return (float) $this->nominal_pembayaran >= self::MINIMAL_DITERIMA;
    }

    /**
     * Terapkan aturan: kalau nominal >= 2.5jt -> status otomatis diterima.
     * Dipanggil setiap kali admin update nominal.
     */
    public function applyAutoAcceptRule(): void
    {
        if ($this->isEligibleForAutoAccept() && $this->status !== self::STATUS_DITOLAK) {
            $this->status = self::STATUS_DITERIMA;
            $this->processed_by = Auth::id();
            $this->processed_at = now();
        }
    }

    /**
     * Nomor HP dirapikan ke format internasional (62xxxx) untuk link WhatsApp
     */
    public function getWhatsappNumberAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->no_hp ?? '');

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }

        return $number;
    }

    public function getWhatsappLinkAttribute(): string
    {
        $pesan = "Halo {$this->nama_lengkap}, terkait pendaftaran Anda dengan nomor {$this->no_pendaftaran}...";

        return "https://wa.me/{$this->whatsapp_number}?text=" . urlencode($pesan);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_DITOLAK => 'Tidak Diterima',
            default => 'Menunggu Verifikasi',
        };
    }

    /**
     * Auto-generate no_pendaftaran saat create
     */
    protected static function booted()
    {
        static::creating(function (Pendaftar $pendaftar) {
            if (empty($pendaftar->no_pendaftaran)) {
                $pendaftar->no_pendaftaran = self::generateNoPendaftaran();
            }
            if (empty($pendaftar->status)) {
                $pendaftar->status = self::STATUS_PENDING;
            }
        });
    }
}
