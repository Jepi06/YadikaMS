<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus duplikat dulu: pertahankan baris dengan id terbesar per kombinasi
        DB::statement('
            DELETE p1 FROM presensi_lms p1
            INNER JOIN presensi_lms p2
                ON p1.pengampu_mapel_id = p2.pengampu_mapel_id
               AND p1.siswa_id = p2.siswa_id
               AND DATE(p1.tanggal) = DATE(p2.tanggal)
               AND p1.id < p2.id
        ');

        Schema::table('presensi_lms', function (Blueprint $table) {
            $table->unique(
                ['pengampu_mapel_id', 'siswa_id', 'tanggal'],
                'presensi_lms_unik_per_hari'
            );
        });
    }

    public function down(): void
    {
        Schema::table('presensi_lms', function (Blueprint $table) {
            $table->dropUnique('presensi_lms_unik_per_hari');
        });
    }
};