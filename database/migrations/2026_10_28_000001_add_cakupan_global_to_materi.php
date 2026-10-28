<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 'angkatan' = semua kelas satu tingkat (perilaku global yang lama, jadi data lama tidak berubah)
        // 'kelas'    = hanya kelas yang dipilih guru (lihat tabel materi_kelas)
        Schema::table('materi', function (Blueprint $t) {
            $t->string('cakupan_global', 20)->default('angkatan')->after('is_global');
        });

        Schema::create('materi_kelas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $t->unsignedBigInteger('kelas_id')->index(); // ADJUST: tambahkan ->constrained('kelas') bila nama tabel kelas Anda 'kelas'
            $t->timestamps();
            $t->unique(['materi_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_kelas');
        Schema::table('materi', function (Blueprint $t) {
            $t->dropColumn('cakupan_global');
        });
    }
};