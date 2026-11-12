<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Koreksi AKTIF (1 baris per siswa per tanggal). Tabel presensi_lms TIDAK disentuh.
        Schema::create('koreksi_absensi_harian_lms', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('kelas_id');
            $t->unsignedBigInteger('siswa_id');
            $t->string('tahun_ajaran', 20);
            $t->string('semester', 20);
            $t->date('tanggal');
            $t->string('status_sistem', 10);   // snapshot hasil hitungan otomatis
            $t->string('status_koreksi', 10);  // Hadir/Izin/Sakit/Alpa
            $t->text('alasan');
            $t->unsignedBigInteger('diubah_oleh');
            $t->string('diubah_oleh_nama');
            $t->timestamps();

            $t->unique(['kelas_id', 'siswa_id', 'tanggal'], 'uniq_koreksi_harian');
            $t->index(['kelas_id', 'tahun_ajaran', 'semester'], 'idx_koreksi_periode');
        });

        // Riwayat/audit: setiap set, ubah, batal tercatat permanen.
        Schema::create('koreksi_absensi_log_lms', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('kelas_id');
            $t->unsignedBigInteger('siswa_id');
            $t->date('tanggal');
            $t->string('aksi', 10);            // set | ubah | batal
            $t->string('status_sistem', 10);
            $t->string('status_lama', 10)->nullable();
            $t->string('status_baru', 10)->nullable();
            $t->text('alasan')->nullable();
            $t->unsignedBigInteger('user_id');
            $t->string('user_nama');
            $t->timestamps();

            $t->index(['kelas_id', 'siswa_id', 'tanggal'], 'idx_koreksi_log');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koreksi_absensi_log_lms');
        Schema::dropIfExists('koreksi_absensi_harian_lms');
    }
};