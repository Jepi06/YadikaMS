<?php

namespace App\Services\Lms;

use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\WaliKelasPeriode;
use App\Models\User;
use App\Support\TahunAjaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Cakupan siswa yang boleh dicatat poinnya:
 *  - Guru mapel      : hanya kelas yang ia ampu (tahun ajaran + semester berjalan)
 *  - Wali kelas      : hanya kelas yang ia walikan
 *  - Guru piket      : PELANGGARAN untuk semua siswa; prestasi hanya di kelas yang ia ampu/walikan
 *  - BK, Kesiswaan, Super admin : semua siswa (pelanggaran & prestasi)
 *
 * Hapus catatan: BK/Kesiswaan/Admin semua catatan, selain itu hanya catatan miliknya.
 * Monitoring piket: Guru Piket, BK, Kesiswaan, Super admin.
 */
class PelanggaranAkses
{
    private static array $cacheRole = [];
    private static array $cacheMengajar = [];
    private static array $cacheWali = [];

    public static function kodeRole(User $u): array
    {
        return self::$cacheRole[$u->id] ??= DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->where('user_role.user_id', $u->id)
            ->pluck('roles.kode')->unique()->values()->all();
    }

    public static function isAdmin(User $u): bool     { return (bool) $u->is_super_admin; }
    public static function isBk(User $u): bool        { return in_array('guru_bk', self::kodeRole($u), true); }
    public static function isKesiswaan(User $u): bool { return in_array('kesiswaan', self::kodeRole($u), true); }
    public static function isPiket(User $u): bool     { return in_array('guru_piket', self::kodeRole($u), true); }

    public static function penuh(User $u): bool
    {
        return self::isAdmin($u) || self::isBk($u) || self::isKesiswaan($u);
    }

    public static function bolehInput(User $u): bool
    {
        return $u->isGuruLms() || self::isAdmin($u);
    }

    public static function bolehMonitoring(User $u): bool
    {
        return self::penuh($u) || self::isPiket($u);
    }

    /** @return int[] kelas yang diampu user (mapel) pada periode berjalan */
    public static function kelasMengajarIds(User $u): array
    {
        return self::$cacheMengajar[$u->id] ??= PengampuMapel::where('guru_id', $u->id)
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->where('semester', TahunAjaran::semesterSekarang())
            ->pluck('kelas_id')->map(fn($i) => (int) $i)->unique()->values()->all();
    }

    /** @return int[] kelas yang diwalikan user pada tahun ajaran berjalan */
    public static function kelasWaliIds(User $u): array
    {
        return self::$cacheWali[$u->id] ??= WaliKelasPeriode::where('user_id', $u->id)
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->pluck('kelas_id')->map(fn($i) => (int) $i)->unique()->values()->all();
    }

    /** Kelas milik sendiri = diampu + diwalikan. */
    public static function kelasSendiriIds(User $u): array
    {
        return array_values(array_unique(array_merge(self::kelasMengajarIds($u), self::kelasWaliIds($u))));
    }

    /** Kelas yang tampil di daftar/pilihan (piket & penuh = semua kelas). */
    public static function kelasBoleh(User $u): Collection
    {
        $q = Kelas::orderBy('nama_kelas');

        if (! (self::penuh($u) || self::isPiket($u))) {
            $q->whereIn('id', self::kelasSendiriIds($u));
        }

        return $q->get();
    }

    /** Kelas yang boleh diberi PRESTASI. null = semua kelas. */
    public static function kelasPrestasiIds(User $u): ?array
    {
        return self::penuh($u) ? null : self::kelasSendiriIds($u);
    }

    /** Boleh mencatat tipe tertentu untuk kelas tertentu? */
    public static function bolehKelasTipe(User $u, int $kelasId, string $tipe): bool
    {
        if (self::penuh($u) || in_array($kelasId, self::kelasSendiriIds($u), true)) {
            return true;
        }

        // guru piket: pelanggaran untuk semua siswa
        return self::isPiket($u) && $tipe === 'pelanggaran';
    }

    public static function cakupanLabel(User $u): string
    {
        return match (true) {
            self::penuh($u)    => 'Semua siswa (pelanggaran & prestasi)',
            self::isPiket($u)  => 'Pelanggaran: semua siswa • Prestasi: kelas yang Anda ampu/walikan',
            default            => 'Kelas yang Anda ampu atau walikan',
        };
    }

    public static function bolehHapus(User $u, $catatan): bool
    {
        return self::penuh($u) || (int) $catatan->dicatat_oleh === (int) $u->id;
    }

    public static function peran(User $u): string
    {
        return match (true) {
            self::isAdmin($u)     => 'admin',
            self::isKesiswaan($u) => 'kesiswaan',
            self::isBk($u)        => 'guru_bk',
            self::isPiket($u)     => 'guru_piket',
            default               => 'guru',
        };
    }

    public static function labelPeran(?string $kode): string
    {
        return ['admin' => 'Admin', 'kesiswaan' => 'Kesiswaan', 'guru_bk' => 'Guru BK',
                'guru_piket' => 'Guru Piket'][$kode] ?? 'Guru';
    }
}
