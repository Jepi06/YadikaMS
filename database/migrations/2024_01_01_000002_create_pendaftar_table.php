<?php
// FILE: database/migrations/2024_01_01_000009_create_pendaftar_table.php
// Dijalankan setelah users dan jurusan.
//
// PENTING: tabel ini TIDAK berelasi sama sekali ke guru_pembimbing atau
// ke modul PKL/LMS — pendaftar murni domain PPDB/SPMB dan berdiri sendiri.
//
// Ada 2 jalur data masuk ke tabel ini:
// 1. Calon siswa mengisi form pendaftaran publik TANPA LOGIN
//    -> created_by = NULL
// 2. Admin PPDB (user dengan role "admin" di modul ppdb, dicek lewat
//    tabel user_role) menambahkan/menginput data secara manual
//    -> created_by = id user admin tersebut

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 20)->unique(); // PPDB-2025-0001

            // Data diri pendaftar
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->string('nama_orang_tua');
            $table->string('asal_sekolah');
            $table->string('no_hp', 20);

            // Jurusan yang dipilih — FK ke tabel jurusan bersama
            $table->foreignId('jurusan_id')
                ->constrained('jurusan')
                ->restrictOnDelete();

            // Status penerimaan
            $table->enum('status', ['pending', 'diterima', 'ditolak'])
                ->default('pending');

            // Nominal pembayaran: >= 2.500.000 -> diterima
            $table->decimal('nominal_pembayaran', 15, 2)->default(0);
            $table->text('catatan_admin')->nullable();

            // Admin PPDB yang MEMPROSES keputusan terima/tolak
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            // Siapa yang MENGINPUT data pendaftaran ini.
            // NULL   = pendaftar mengisi sendiri lewat form publik (tanpa login)
            // terisi = diinput manual oleh admin PPDB
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Index untuk performa query filter
            $table->index('status');
            $table->index('jurusan_id');
            $table->index('no_hp');
            $table->index('no_pendaftaran');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar');
    }
};
