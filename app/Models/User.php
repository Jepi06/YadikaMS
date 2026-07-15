<?php

namespace App\Models;

use App\Models\Mapping\Siswa;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
            ->withPivot(['assigned_by', 'assigned_at']);
    }

    // ── Helper: cek akses per sistem ──────────────────────────

    public function hasModuleAccess(string $moduleKode): bool
    {
        return $this->is_active && $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->exists();
    }

    public function hasPklAccess(): bool
    {
        return $this->hasModuleAccess('pkl');
    }

    public function hasPpdbAccess(): bool
    {
        return $this->hasModuleAccess('ppdb');
    }

    public function hasLmsAccess(): bool
    {
        return $this->hasModuleAccess('lms');
    }

    public function hasRoleInModule(string $moduleKode, string ...$roleKodes): bool
    {
        return $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->whereIn('kode', $roleKodes)
            ->exists();
    }

    public function hasPklRole(string ...$roles): bool
    {
        return $this->hasRoleInModule('pkl', ...$roles);
    }

    public function hasPpdbRole(string ...$roles): bool
    {
        return $this->hasRoleInModule('ppdb', ...$roles);
    }

    public function isSiswa(): bool
    {
        return $this->hasPklRole('siswa');
    }

    public function isApproverPkl(): bool
    {
        return $this->hasPklRole('wali_kelas', 'guru_bk', 'kesiswaan', 'kepala_jurusan');
    }

    /** BARU: dipakai middleware/route untuk membatasi menu CRUD ke admin & hubin saja */
    public function isAdminAtauHubin(): bool
    {
        return $this->hasPklRole('admin');
    }

    // ── Accessors ─────────────────────────────────────────────

    protected function labelForModule(string $moduleKode, array $labels): string
    {
        $kodeRoles = $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->pluck('kode');

        if ($kodeRoles->isEmpty()) {
            return '-';
        }

        return $kodeRoles->map(fn($k) => $labels[$k] ?? $k)->implode(', ');
    }

    public function getRolePklLabelAttribute(): string
    {
        return $this->labelForModule('pkl', [
            'admin' => 'Admin PKL',
            'wali_kelas' => 'Wali Kelas',
            'guru_bk' => 'Guru BK',
            'kesiswaan' => 'Kesiswaan',
            'kepala_jurusan' => 'Kepala Jurusan',
            'siswa' => 'Siswa',
        ]);
    }

    public function getRolePpdbLabelAttribute(): string
    {
        return $this->labelForModule('ppdb', [
            'admin' => 'Admin PPDB',
        ]);
    }

    public function getRoleLmsLabelAttribute(): string
    {
        return $this->labelForModule('lms', [
            'admin' => 'Admin LMS',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
        ]);
    }
}
