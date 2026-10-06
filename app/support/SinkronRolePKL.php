<?php

namespace App\Support;

use App\Models\Lms\PengampuMapel;
use App\Models\Lms\WaliKelasPeriode;
use App\Models\Mapping\PenempatanPkl;
use App\Models\Role;
use App\Models\User;

/**
 * Sinkronkan role PKL otomatis dari data penugasan LMS:
 *  - jadi wali kelas (periode aktif)       -> role PKL 'wali_kelas'
 *  - mengajar mapel BK (periode aktif)     -> role PKL 'guru_bk'
 *
 * Cara pakai:
 *   SinkronRolePkl::untuk($guru);          // hanya MENAMBAH role kalau memenuhi syarat
 *   SinkronRolePkl::untuk($guru, true);    // juga MENCABUT role kalau sudah tidak memenuhi syarat
 *
 * Mode cabut hanya dipakai saat penugasan dihapus, supaya role yang
 * diberikan manual lewat "Kelola Pengguna" tidak ikut terhapus tanpa sebab.
 */
class SinkronRolePkl
{
    private const KODE_MODUL_PKL = 'pkl';
    private const ROLE_WALI_KELAS = 'wali_kelas';
    private const ROLE_GURU_BK = 'guru_bk';

    public static function untuk(User|int|null $user, bool $cabutKalauTidakBerlaku = false): void
    {
        $user = $user instanceof User ? $user : ($user ? User::find($user) : null);

        if (! $user) {
            return;
        }

        $tahunAjaran = TahunAjaran::sekarang();
        $semester = TahunAjaran::semesterSekarang();

        $adalahWaliKelas = WaliKelasPeriode::where('user_id', $user->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->exists();

        $mengajarBk = PengampuMapel::where('guru_id', $user->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->whereHas('mataPelajaran', fn($m) => $m->where('kode', PenempatanPkl::KODE_MAPEL_BK))
            ->exists();

        self::atur($user, self::ROLE_WALI_KELAS, $adalahWaliKelas, $cabutKalauTidakBerlaku);
        self::atur($user, self::ROLE_GURU_BK, $mengajarBk, $cabutKalauTidakBerlaku);
    }

    private static function atur(User $user, string $kodeRole, bool $berlaku, bool $cabut): void
    {
        $roleId = Role::whereHas('module', fn($m) => $m->where('kode', self::KODE_MODUL_PKL))
            ->where('kode', $kodeRole)
            ->value('id');

        if (! $roleId) {
            return;
        }

        if ($berlaku) {
            if (! $user->roles()->where('role_id', $roleId)->exists()) {
                $user->roles()->attach($roleId, ['assigned_at' => now()]);
            }
        } elseif ($cabut) {
            $user->roles()->detach($roleId);
        }
    }
}