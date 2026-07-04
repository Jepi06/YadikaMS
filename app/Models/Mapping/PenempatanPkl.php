<?php

namespace App\Models\Mapping;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $siswa_id
 * @property int $tempat_pkl_id
 * @property int $guru_pembimbing_id
 * @property \Illuminate\Support\Carbon $tanggal_mulai
 * @property \Illuminate\Support\Carbon $tanggal_selesai
 * @property string $tahun_ajaran
 * @property string|null $keterangan
 * @property string $status
 * @property string $status_wali_kelas
 * @property string|null $catatan_wali_kelas
 * @property \Illuminate\Support\Carbon|null $approved_at_wali_kelas
 * @property int|null $approved_by_wali_kelas
 * @property string $status_guru_bk
 * @property string|null $catatan_guru_bk
 * @property \Illuminate\Support\Carbon|null $approved_at_guru_bk
 * @property int|null $approved_by_guru_bk
 * @property string $status_kesiswaan
 * @property string|null $catatan_kesiswaan
 * @property \Illuminate\Support\Carbon|null $approved_at_kesiswaan
 * @property int|null $approved_by_kesiswaan
 * @property string $status_kepala_jurusan
 * @property string|null $catatan_kepala_jurusan
 * @property \Illuminate\Support\Carbon|null $approved_at_kepala_jurusan
 * @property int|null $approved_by_kepala_jurusan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $approverGuruBk
 * @property-read User|null $approverKepalaJurusan
 * @property-read User|null $approverKesiswaan
 * @property-read User|null $approverWaliKelas
 * @property-read int $approval_progress
 * @property-read string|null $current_approval_stage
 * @property-read array $pending_approvers
 * @property-read string $status_badge
 * @property-read \App\Models\Mapping\GuruPembimbing $guruPembimbing
 * @property-read \App\Models\Mapping\Siswa $siswa
 * @property-read \App\Models\Mapping\TempatPkl $tempatPkl
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereGuruPembimbingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTahunAjaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTempatPklId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PenempatanPkl extends Model
{
    protected $table = 'penempatan_pkl';

    protected $fillable = [
        'siswa_id', 'tempat_pkl_id', 'guru_pembimbing_id',
        'tanggal_mulai', 'tanggal_selesai', 'tahun_ajaran', 'keterangan', 'status',
        'status_wali_kelas', 'catatan_wali_kelas', 'approved_at_wali_kelas', 'approved_by_wali_kelas',
        'status_guru_bk', 'catatan_guru_bk', 'approved_at_guru_bk', 'approved_by_guru_bk',
        'status_kesiswaan', 'catatan_kesiswaan', 'approved_at_kesiswaan', 'approved_by_kesiswaan',
        'status_kepala_jurusan', 'catatan_kepala_jurusan', 'approved_at_kepala_jurusan', 'approved_by_kepala_jurusan',
    ];

    protected $casts = [
        'tanggal_mulai'             => 'date',
        'tanggal_selesai'           => 'date',
        'approved_at_wali_kelas'    => 'datetime',
        'approved_at_guru_bk'       => 'datetime',
        'approved_at_kesiswaan'     => 'datetime',
        'approved_at_kepala_jurusan'=> 'datetime',
    ];

    /**
     * Urutan tahapan approval PKL. Dipakai berulang kali di bawah,
     * jadi disatukan di sini supaya tidak duplikat & gampang diubah urutannya.
     */
    private const APPROVAL_CHAIN = [
        'wali_kelas'     => 'Wali Kelas',
        'guru_bk'        => 'Guru BK',
        'kesiswaan'      => 'Kesiswaan',
        'kepala_jurusan' => 'Kepala Jurusan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tempatPkl()
    {
        return $this->belongsTo(TempatPkl::class);
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(GuruPembimbing::class);
    }

    public function approverWaliKelas()
    {
        return $this->belongsTo(User::class, 'approved_by_wali_kelas');
    }

    public function approverGuruBk()
    {
        return $this->belongsTo(User::class, 'approved_by_guru_bk');
    }

    public function approverKesiswaan()
    {
        return $this->belongsTo(User::class, 'approved_by_kesiswaan');
    }

    public function approverKepalaJurusan()
    {
        return $this->belongsTo(User::class, 'approved_by_kepala_jurusan');
    }

    /**
     * Cek apakah user tertentu bisa approve berdasarkan role & urutan.
     *
     * PERBAIKAN: sebelumnya memakai $user->role, padahal kolom yang benar
     * pada model User adalah role_pkl (lihat User::$fillable dan
     * RoleMiddleware yang sudah memakai role_pkl dengan benar). Akibat bug
     * ini, canApproveBy() SELALU mengembalikan false untuk semua user,
     * karena $user->role tidak pernah ada nilainya.
     */
    public function canApproveBy(User $user): bool
    {
        return match ($user->role_pkl) {
            'wali_kelas'     => $this->status === 'diajukan' && $this->status_wali_kelas === 'pending',
            'guru_bk'        => $this->status_wali_kelas === 'approved' && $this->status_guru_bk === 'pending',
            'kesiswaan'      => $this->status_guru_bk === 'approved' && $this->status_kesiswaan === 'pending',
            'kepala_jurusan' => $this->status_kesiswaan === 'approved' && $this->status_kepala_jurusan === 'pending',
            default          => false,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'    => '<span class="badge bg-secondary">Draft</span>',
            'diajukan' => '<span class="badge bg-primary">Diajukan</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default    => '<span class="badge bg-light text-dark">' . $this->status . '</span>',
        };
    }

    /**
     * Hitung progres approval (0–4)
     */
    public function getApprovalProgressAttribute(): int
    {
        $count = 0;
        foreach (array_keys(self::APPROVAL_CHAIN) as $key) {
            if ($this->{"status_{$key}"} === 'approved') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Daftar nama tahapan yang MASIH menunggu approval, dalam urutan proses.
     * Dipakai supaya siswa bisa melihat "sedang menunggu approval siapa saja".
     *
     * - draft    -> siswa belum mengajukan, belum masuk antrian siapapun
     * - rejected -> proses berhenti, tidak ada yang pending lagi
     * - approved -> semua sudah selesai, tidak ada yang pending
     */
    public function getPendingApproversAttribute(): array
    {
        if (in_array($this->status, ['draft', 'rejected', 'approved'])) {
            return [];
        }

        $pending = [];
        foreach (self::APPROVAL_CHAIN as $key => $label) {
            if ($this->{"status_{$key}"} === 'pending') {
                $pending[] = $label;
            }
        }

        return $pending;
    }

    /**
     * Nama tahapan yang SEDANG giliran approve saat ini (paling depan di antrian).
     * Berguna untuk ditampilkan sebagai "posisi pengajuan sekarang".
     */
    public function getCurrentApprovalStageAttribute(): ?string
    {
        return $this->pending_approvers[0] ?? null;
    }

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', ['rejected']);
    }
}
