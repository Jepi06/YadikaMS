<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daftar kegiatan praktik (boleh banyak per pengampu_mapel)
        if (! Schema::hasTable('praktik_lms')) {
            Schema::create('praktik_lms', function (Blueprint $t) {
                $t->id();
                $t->foreignId('pengampu_mapel_id')->constrained('pengampu_mapel')->cascadeOnDelete();
                $t->string('judul');
                $t->date('tanggal')->nullable();
                $t->text('deskripsi')->nullable();
                $t->timestamps();
            });
        }

        // Nilai praktik per siswa per kegiatan praktik
        if (! Schema::hasTable('nilai_praktik_lms')) {
            Schema::create('nilai_praktik_lms', function (Blueprint $t) {
                $t->id();
                $t->foreignId('praktik_id')->constrained('praktik_lms')->cascadeOnDelete();
                $t->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
                $t->decimal('nilai', 5, 2)->nullable();
                $t->string('catatan')->nullable();
                $t->timestamps();
                $t->unique(['praktik_id', 'siswa_id']);
            });
        }

        // Pengaturan komponen nilai (praktik & sikap) per pengampu_mapel
        if (! Schema::hasTable('pengaturan_nilai_lms')) {
            Schema::create('pengaturan_nilai_lms', function (Blueprint $t) {
                $t->id();
                $t->foreignId('pengampu_mapel_id')->unique()->constrained('pengampu_mapel')->cascadeOnDelete();
                $t->boolean('praktik_gabung_tugas')->default(true);
                $t->unsignedTinyInteger('bobot_praktik')->default(0);
                $t->string('sikap_mode', 10)->default('utama'); // utama | tambahan
                $t->unsignedTinyInteger('sikap_maks_tambahan')->default(10);
                $t->timestamps();
            });
        }

        // Nilai sikap custom (manual) di samping predikat dropdown
        if (Schema::hasTable('nilai_sikap_lms')) {
            Schema::table('nilai_sikap_lms', function (Blueprint $t) {
                if (! Schema::hasColumn('nilai_sikap_lms', 'is_custom')) {
                    $t->boolean('is_custom')->default(false);
                }
                if (! Schema::hasColumn('nilai_sikap_lms', 'nilai_custom')) {
                    $t->decimal('nilai_custom', 5, 2)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_praktik_lms');
        Schema::dropIfExists('praktik_lms');
        Schema::dropIfExists('pengaturan_nilai_lms');

        if (Schema::hasTable('nilai_sikap_lms')) {
            Schema::table('nilai_sikap_lms', function (Blueprint $t) {
                if (Schema::hasColumn('nilai_sikap_lms', 'nilai_custom')) {
                    $t->dropColumn('nilai_custom');
                }
                if (Schema::hasColumn('nilai_sikap_lms', 'is_custom')) {
                    $t->dropColumn('is_custom');
                }
            });
        }
    }
};
