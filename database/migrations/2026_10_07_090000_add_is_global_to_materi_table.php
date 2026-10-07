<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsGlobalToMateriTable extends Migration
{
    public function up()
    {
        Schema::table('materi', function (Blueprint $table) {
            // false = khusus kelas pembuat materi, true = bisa dibuka kelas lain (mapel & tahun ajaran sama)
            $table->boolean('is_global')->default(false)->index();
        });
    }

    public function down()
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropIndex(['is_global']);
            $table->dropColumn('is_global');
        });
    }
}