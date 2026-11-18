<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_lms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pengampu_mapel_id')->constrained('pengampu_mapel')->cascadeOnDelete();
            $t->string('judul', 150);
            $t->text('deskripsi')->nullable();
            $t->unsignedSmallInteger('durasi_menit')->default(30);
            $t->dateTime('mulai_pada')->nullable();      // jendela boleh mulai mengerjakan
            $t->dateTime('selesai_pada')->nullable();
            $t->boolean('acak_soal')->default(false);
            $t->unsignedTinyInteger('maks_percobaan')->default(1);
            $t->boolean('tampil_pembahasan')->default(false);
            $t->string('status', 10)->default('draft');  // draft | terbit | ditutup
            $t->timestamps();
        });

        Schema::create('soal_quiz_lms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_id')->constrained('quiz_lms')->cascadeOnDelete();
            $t->unsignedSmallInteger('urutan');
            $t->text('pertanyaan');
            $t->text('opsi_a');
            $t->text('opsi_b');
            $t->text('opsi_c')->nullable();
            $t->text('opsi_d')->nullable();
            $t->text('opsi_e')->nullable();
            $t->char('kunci', 1);
            $t->decimal('poin', 5, 2)->default(1);
            $t->text('pembahasan')->nullable();
            $t->timestamps();

            $t->index(['quiz_id', 'urutan']);
        });

        Schema::create('percobaan_quiz_lms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_id')->constrained('quiz_lms')->cascadeOnDelete();
            $t->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $t->dateTime('mulai_at');
            $t->dateTime('deadline_at');
            $t->dateTime('selesai_at')->nullable();
            $t->string('status', 10)->default('berjalan');   // berjalan | selesai
            $t->text('urutan_soal')->nullable();              // JSON id soal (urutan tampil siswa ini)
            $t->decimal('poin_didapat', 7, 2)->default(0);
            $t->decimal('poin_total', 7, 2)->default(0);
            $t->unsignedSmallInteger('benar')->default(0);
            $t->unsignedSmallInteger('total_soal')->default(0);
            $t->decimal('nilai', 5, 2)->nullable();
            $t->timestamps();

            $t->index(['quiz_id', 'siswa_id']);
        });

        Schema::create('jawaban_quiz_lms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('percobaan_id')->constrained('percobaan_quiz_lms')->cascadeOnDelete();
            $t->foreignId('soal_id')->constrained('soal_quiz_lms')->cascadeOnDelete();
            $t->char('jawaban', 1)->nullable();
            $t->boolean('benar')->nullable();
            $t->timestamps();

            $t->unique(['percobaan_id', 'soal_id']);
        });

        // Opsi nilai quiz per mapel. Default 'nonaktif' → nilai akhir mapel yang ada TIDAK berubah.
        Schema::table('bobot_nilai_lms', function (Blueprint $t) {
            $t->string('quiz_mode', 10)->default('nonaktif');            // nonaktif | include | tambahan
            $t->unsignedTinyInteger('bobot_quiz')->default(0);           // dipakai bila include
            $t->unsignedTinyInteger('quiz_bonus_persen')->default(10);   // dipakai bila tambahan
            $t->unsignedTinyInteger('quiz_bonus_maks')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('bobot_nilai_lms', function (Blueprint $t) {
            $t->dropColumn(['quiz_mode', 'bobot_quiz', 'quiz_bonus_persen', 'quiz_bonus_maks']);
        });
        Schema::dropIfExists('jawaban_quiz_lms');
        Schema::dropIfExists('percobaan_quiz_lms');
        Schema::dropIfExists('soal_quiz_lms');
        Schema::dropIfExists('quiz_lms');
    }
};
