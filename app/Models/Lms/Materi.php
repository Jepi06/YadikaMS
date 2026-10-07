<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_path',
        'link_url',
        'urutan',
        'mode_akses',
        'dibuka_manual',
        'buka_pada',
        'is_global',
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
     * dengan mata pelajaran & tahun ajaran yang sama.
     *
     * Pemakaian: Materi::terlihatUntuk($pengampuMapel)->orderBy('urutan')->get();
     */
    public function scopeTerlihatUntuk($query, PengampuMapel $pm)
    {
        return $query->where(function ($q) use ($pm) {
            $q->where('pengampu_mapel_id', $pm->id)
                ->orWhere(function ($q) use ($pm) {
                    $q->where('is_global', true)
                        ->whereHas('pengampuMapel', fn ($p) => $p
                            ->where('mata_pelajaran_id', $pm->mata_pelajaran_id)
                            ->where('tahun_ajaran', $pm->tahun_ajaran));
                });
        });
    }

    /**
     * Apakah siswa ini boleh mengakses materi (milik kelasnya, atau global untuk mapel yang sama).
     * Dipakai untuk pengecekan izin unduh file (route lms.file.materi).
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

        // Global: siswa harus punya pengampu mapel yang sama (mapel + tahun ajaran) di kelasnya
        if ($this->is_global && isset($siswa->kelas_id)) {
            return PengampuMapel::where('kelas_id', $siswa->kelas_id)
                ->where('mata_pelajaran_id', $pm->mata_pelajaran_id)
                ->where('tahun_ajaran', $pm->tahun_ajaran)
                ->exists();
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