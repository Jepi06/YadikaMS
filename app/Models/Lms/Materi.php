<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'pengantar',        // ← BARU: teks popup pengantar
        'file_path',
        'link_url',
        'urutan',
        'mode_akses',
        'dibuka_manual',
        'buka_pada',
        'is_global',
        'cakupan_global',   // ← BARU: 'angkatan' | 'kelas' (daftar kelas ada di tabel materi_kelas)
    ];

    protected $casts = [
        'dibuka_manual' => 'boolean',
        'buka_pada' => 'datetime',
        'is_global' => 'boolean',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    /**
     * Materi yang boleh dilihat pada sebuah pengampu mapel (kelas):
     * milik kelas itu sendiri + materi global dari kelas lain
     * (mata pelajaran & tahun ajaran sama) yang CAKUPANNYA mencakup kelas ini:
     *   - cakupan 'angkatan' → kelas asal materi satu tingkat dengan kelas ini DAN diampu guru yang sama
     *   - cakupan 'kelas'    → kelas ini dipilih guru (tabel materi_kelas)
     *
     * Pemakaian: Materi::terlihatUntuk($pengampuMapel)->orderBy('urutan')->get();
     */
    public function scopeTerlihatUntuk($query, PengampuMapel $pm)
    {
        $pm->loadMissing('kelas');
        $tingkat = $pm->kelas->tingkat ?? null;

        return $query->where(function ($q) use ($pm, $tingkat) {
            $q->where('pengampu_mapel_id', $pm->id)
                ->orWhere(function ($q) use ($pm, $tingkat) {
                    $q->where('is_global', true)
                        ->whereHas('pengampuMapel', fn ($p) => $p
                            ->where('mata_pelajaran_id', $pm->mata_pelajaran_id)
                            ->where('tahun_ajaran', $pm->tahun_ajaran))
                        ->where(function ($c) use ($pm, $tingkat) {
                            $c->where(fn ($a) => $a
                                    ->where('cakupan_global', 'angkatan')
                                    // hanya materi dari guru yang SAMA dengan pengampu mapel di kelas ini
                                    ->whereHas('pengampuMapel', fn ($p) => $p->where('guru_id', $pm->guru_id))
                                    ->whereHas('pengampuMapel.kelas', fn ($k) => $k->where('tingkat', $tingkat)))
                              ->orWhere(fn ($a) => $a
                                    ->where('cakupan_global', 'kelas')
                                    ->whereIn('id', DB::table('materi_kelas')
                                        ->where('kelas_id', $pm->kelas_id)
                                        ->select('materi_id')));
                        });
                });
        });
    }

    /**
     * Apakah siswa ini boleh mengakses materi (milik kelasnya, atau global yang cakupannya
     * mencakup kelasnya). Dipakai untuk izin unduh file (route lms.file.materi)
     * dan tandai selesai.
     */
    public function bisaDiaksesSiswa(?\App\Models\Siswa $siswa): bool
    {
        if (! $siswa) {
            return false;
        }

        $pm = $this->pengampuMapel;
        if (! $pm) {
            return false;
        }

        // Milik kelas siswa sendiri
        if (isset($siswa->kelas_id) && $pm->kelas_id == $siswa->kelas_id) {
            return true;
        }

        // Global: siswa harus punya pengampu mapel yang sama (mapel + tahun ajaran) di kelasnya,
        // dan cakupan materi harus mencakup kelas siswa.
        if ($this->is_global && isset($siswa->kelas_id)) {
            $pmSiswa = PengampuMapel::with('kelas')
                ->where('kelas_id', $siswa->kelas_id)
                ->where('mata_pelajaran_id', $pm->mata_pelajaran_id)
                ->where('tahun_ajaran', $pm->tahun_ajaran)
                ->first();

            return $pmSiswa
                && static::terlihatUntuk($pmSiswa)->whereKey($this->id)->exists();
        }

        return false;
    }

    /**
     * Return null kalau materi TERBUKA untuk siswa ini, atau string
     * alasan kenapa masih TERKUNCI.
     */
    public function alasanTerkunci(?\App\Models\Siswa $siswa): ?string
    {
        if (! $siswa) {
            return null; // bukan siswa (misal guru pemilik) — gak dikunci
        }

        switch ($this->mode_akses) {
            case 'manual':
                return $this->dibuka_manual ? null : 'Materi ini akan dibuka oleh guru.';

            case 'tanggal':
                if ($this->buka_pada && now()->lt($this->buka_pada)) {
                    return 'Materi dibuka pada ' . $this->buka_pada->translatedFormat('d M Y, H:i') . '.';
                }
                return null;

            case 'berurutan':
                $sebelumnya = self::where('pengampu_mapel_id', $this->pengampu_mapel_id)
                    ->where(function ($q) {
                        $q->where('urutan', '<', $this->urutan)
                            ->orWhere(fn($qq) => $qq->where('urutan', $this->urutan)->where('id', '<', $this->id));
                    })
                    ->orderByDesc('urutan')
                    ->orderByDesc('id')
                    ->first();

                if (! $sebelumnya) {
                    return null; // materi pertama, gak ada yang harus diselesaikan dulu
                }

                $sudahSelesai = MateriSelesai::where('materi_id', $sebelumnya->id)
                    ->where('siswa_id', $siswa->id)
                    ->exists();

                return $sudahSelesai ? null : 'Selesaikan dulu materi "' . $sebelumnya->judul . '".';

            default:
                return null;
        }
    }
}