<?php
// FILE: database/migrations/2024_01_01_000003_create_jurusan_table.php
// Satu tabel jurusan dipakai bersama oleh PKL, PPDB, dan LMS.
// Dijalankan setelah users & module_role_access.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();

            // Kolom yang dipakai PKL
            $table->string('kode', 10)->unique(); // RPL, AKL, PHT
            $table->string('nama'); // Nama lengkap jurusan

            // Kolom tambahan untuk PPDB
            $table->text('deskripsi')->nullable();
            $table->integer('kuota')->default(0); // Kuota penerimaan siswa baru

            $table->timestamps();
        });

        // ── Seed data jurusan ───────────────────────────────────
        DB::table('jurusan')->insert([
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak Dan Gim',
                'deskripsi' => 'Mempelajari pengembangan software dan game development',
                'kuota' => 72,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'AK',
                'nama' => 'Akuntansi',
                'deskripsi' => 'Mempelajari ilmu akuntansi dan keuangan bisnis',
                'kuota' => 72,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'HTL',
                'nama' => 'Perhotelan',
                'deskripsi' => 'Mempelajari manajemen perhotelan dan pariwisata',
                'kuota' => 72,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};
