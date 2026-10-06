<?php

namespace App\Models\Mapping;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Lms\WaliKelasPeriode;
use App\Support\TahunAjaran;

class PenempatanPkl extends Model
{
    protected $table = 'penempatan_pkl';

    /** Ganti di sini kalau kode mata pelajaran BK di data Anda beda dari 'BK' */
    public const KODE_MAPEL_BK = 'BK';

    protected $fillable = [
        'siswa_id',
        'tempat_pkl_id',
        'guru_pembimbing_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tahun_ajaran',
        'keterangan',
        'status',
        'status_wali_kelas',
        'catatan_wali_kelas',
        'approved_at_wali_kelas',
        'approved_by_wali_kelas',
        'status_guru_bk',
        'catatan_guru_bk',
        'approved_at_guru_bk',
        'approved_by_guru_bk',
        'status_kesiswaan',
        'catatan_kesiswaan',
        'approved_at_kesiswaan',
        'approved_by_kesiswaan',
        'status_kepala_jurusan',
        'catatan_kepala_jurusan',
        'approved_at_kepala_jurusan',
        'approved_by_kepala_jurusan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at_wali_kelas' => 'datetime',
        'approved_at_guru_bk' => 'datetime',
        'approved_at_kesiswaan' => 'datetime',
        'approved_at_kepala_jurusan' => 'datetime',
    ];

    /**
     * Urutan tahapan approval PKL. Dipakai berulang kali di bawah,
     * jadi disatukan di sini supaya tidak duplikat & gampang diubah urutannya.
     */
    private const APPROVAL_CHAIN = [
        'wali_kelas' => 'Wali Kelas',
        'guru_bk' => 'Guru BK',
        'kesiswaan' => 'Kesiswaan',
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
     * PERBAIKAN BESAR: role sekarang many-to-many (satu user bisa punya lebih
     * dari satu role PKL sekaligus, misal admin yang juga wali_kelas), jadi
     * tidak bisa lagi pakai match() atas satu nilai $user->role_pkl (yang
     * sekarang malah sudah tidak ada). Method ini mengembalikan tahap MANA
     * yang sedang bisa diproses oleh $user, dengan scoping:
     *  - wali_kelas    : hanya kalau dia wali kelas dari kelas siswa ini
     *  - guru_bk       : hanya kalau dia mengampu mapel BK di kelas siswa ini
     *  - kesiswaan     : tanpa scope
     *  - kepala_jurusan: hanya kalau dia kepala dari jurusan siswa ini
     *
     * canApproveBy() dan ApprovalController keduanya pakai method ini supaya
     * logikanya cuma ada di satu tempat.
     */
    public function tahapUntukUser(User $user): ?string
    {
        if ($this->status !== 'diajukan') {
            return null;
        }

        if (
            $user->hasPklRole('wali_kelas')
            && $this->status_wali_kelas === 'pending'
            && $this->diampuOlehWaliKelas($user)
        ) {
            return 'wali_kelas';
        }

        if (
            $user->hasPklRole('guru_bk')
            && $this->status_wali_kelas === 'approved'
            && $this->status_guru_bk === 'pending'
            && $this->diampuOlehGuruBk($user)
        ) {
            return 'guru_bk';
        }

        if (
            $user->hasPklRole('kesiswaan')
            && $this->status_guru_bk === 'approved'
            && $this->status_kesiswaan === 'pending'
        ) {
            return 'kesiswaan';
        }

        if (
            $user->hasPklRole('kepala_jurusan')
            && $this->status_kesiswaan === 'approved'
            && $this->status_kepala_jurusan === 'pending'
            && $this->diampuOlehKepalaJurusan($user)
        ) {
            return 'kepala_jurusan';
        }

        return null;
    }

    public function canApproveBy(User $user): bool
    {
        return $this->tahapUntukUser($user) !== null;
    }
    /**
     * Pengajuan yang sedang menunggu approval dari $user (sesuai tahap & scope-nya).
     */
    public static function menungguApproval(User $user)
    {
        $peran = [
            'wali_kelas'     => $user->hasPklRole('wali_kelas'),
            'guru_bk'        => $user->hasPklRole('guru_bk'),
            'kesiswaan'      => $user->hasPklRole('kesiswaan'),
            'kepala_jurusan' => $user->hasPklRole('kepala_jurusan'),
        ];

        if (! in_array(true, $peran, true)) {
            return collect();
        }

        return static::with('siswa.kelas.jurusan')
            ->where('status', 'diajukan')
            ->where(function ($q) use ($peran) {
                if ($peran['wali_kelas']) {
                    $q->orWhere('status_wali_kelas', 'pending');
                }
                if ($peran['guru_bk']) {
                    $q->orWhere(fn($x) => $x->where('status_wali_kelas', 'approved')
                        ->where('status_guru_bk', 'pending'));
                }
                if ($peran['kesiswaan']) {
                    $q->orWhere(fn($x) => $x->where('status_guru_bk', 'approved')
                        ->where('status_kesiswaan', 'pending'));
                }
                if ($peran['kepala_jurusan']) {
                    $q->orWhere(fn($x) => $x->where('status_kesiswaan', 'approved')
                        ->where('status_kepala_jurusan', 'pending'));
                }
            })
            ->get()
            ->filter(fn($p) => $p->canApproveBy($user))
            ->values();
    }
    private function diampuOlehWaliKelas(User $user): bool
    {
        $this->loadMissing('siswa.kelas');

        if (! $this->siswa) {
            return false;
        }

        if ((int) ($this->siswa->kelas?->wali_kelas_id) === (int) $user->id) {
            return true;
        }

        return WaliKelasPeriode::where('user_id', $user->id)
            ->where('kelas_id', $this->siswa->kelas_id)
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->where('semester', TahunAjaran::semesterSekarang())
            ->exists();
    }

    private function diampuOlehKepalaJurusan(User $user): bool
    {
        $this->loadMissing('siswa.kelas.jurusan');

        return $this->siswa?->kelas?->jurusan?->kepala_jurusan_id === $user->id;
    }

    private function diampuOlehGuruBk(User $user): bool
    {
        $this->loadMissing('siswa');

        if (!$this->siswa) {
            return false;
        }

        return DB::table('pengampu_mapel')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'pengampu_mapel.mata_pelajaran_id')
            ->where('pengampu_mapel.guru_id', $user->id)
            ->where('pengampu_mapel.kelas_id', $this->siswa->kelas_id)
            ->where('mata_pelajaran.kode', self::KODE_MAPEL_BK)
            ->exists();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'diajukan' => '<span class="badge bg-primary">Diajukan</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-light text-dark">' . $this->status . '</span>',
        };
    }

    /** Hitung progres approval (0–4) */
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
     * Info status PKL untuk siswa yang login di LMS (null kalau bukan siswa).
     */
    public static function infoSiswaLms(User $user): ?array
    {
        if (! $user->isSiswaLms()) {
            return null;
        }

        try {
            // ⚠ SESUAIKAN: cara menemukan data Siswa dari akun LMS ini.
            $siswa = method_exists($user, 'siswa') ? $user->siswa : null;
            $siswa ??= Siswa::where('user_id', $user->id)->first();

            if (! $siswa) {
                return null;
            }

            $p = static::with('tempatPkl')
                ->where('siswa_id', $siswa->id)
                ->latest()
                ->first();

            $dibuka = PengaturanPkl::pengajuanDibuka();
            $kelasTarget = PengaturanPkl::kelasTarget($siswa->kelas);
        } catch (\Throwable $e) {
            return null;
        }

        // Belum mengajukan: notif HANYA kalau admin membuka pengajuan & siswa kelas XII
        if (! $p && ! ($dibuka && $kelasTarget)) {
            return null;
        }

        $state = match (true) {
            ! $p                       => 'belum',
            $p->status === 'rejected'  => 'ditolak',
            $p->status === 'draft'     => 'draft',
            $p->status === 'approved'  => 'approved',
            default                    => 'diajukan',
        };

        $kunciCari = $siswa->nis ?: $siswa->nama;
        $perluAjukan = in_array($state, ['belum', 'ditolak']);

        return [
            'state'    => $state,
            'progress' => $p?->approval_progress ?? 0,
            'tempat'   => $p?->tempatPkl?->nama_tempat,
            'url'      => ($perluAjukan && $dibuka)
                ? route('pkl.pengajuan.create')
                : route('pkl', ['search' => $kunciCari]),
        ];
    }
    public function getCurrentApprovalStageAttribute(): ?string
    {
        return $this->pending_approvers[0] ?? null;
    }

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', ['rejected']);
    }
}
