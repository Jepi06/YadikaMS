<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan HARUS seperti ini karena mengikuti dependensi FK
     * (mis. Siswa butuh Kelas, PenempatanPkl butuh Siswa+TempatPkl+
     * GuruPembimbing, dst).
     *
     * Catatan: user admin, modules, roles, dan jurusan SUDAH di-seed
     * lewat migrasi (2024_01_01_000001, 000002, 000003) sebagai
     * data dasar wajib. Seeder di sini menambahkan data dummy
     * untuk kebutuhan development/testing.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // guru/staff + kombinasi role lintas modul
            KelasSeeder::class,          // rombel per jurusan & tingkat
            SiswaSeeder::class,          // siswa per kelas
            TempatPklSeeder::class,      // perusahaan/instansi mitra PKL
            GuruPembimbingSeeder::class, // pembimbing lapangan (master data)
            PenempatanPklSeeder::class,  // penempatan PKL + alur approval
            PendaftarSeeder::class,      // pendaftar SPMB (mandiri & input admin)
            MataPelajaranSeeder::class,  // master mapel untuk LMS
            PengampuMapelSeeder::class,  // guru mengajar mapel di kelas
            MateriSeeder::class,
            TugasSeeder::class,
            PengumpulanTugasSeeder::class,
            PresensiLmsSeeder::class,
        ]);
    }
}
// nomer 6 kalkulus lanjut

