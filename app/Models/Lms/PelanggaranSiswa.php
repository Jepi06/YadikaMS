<?php

namespace App\Models\Lms;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PelanggaranSiswa extends Model
{
    protected $table = 'pelanggaran_siswa_lms';

    protected $fillable = [
        'siswa_id', 'kelas_id', 'jenis_pelanggaran_id', 'tipe', 'kategori', 'nama_pelanggaran',
        'poin', 'tanggal', 'catatan', 'dicatat_oleh', 'peran_pencatat', 'tahun_ajaran', 'semester',
    ];

    protected $casts = ['tanggal' => 'date', 'poin' => 'integer'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
