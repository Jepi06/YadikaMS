<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mou_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tempat_pkl_id')->constrained('tempat_pkl')->cascadeOnDelete();
            $table->string('judul')->nullable();
            $table->string('nomor_mou', 100)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();   // null = tanpa batas waktu
            $table->text('keterangan')->nullable();

            $table->string('file_path');                    // path di disk 'local' (privat)
            $table->string('file_nama_asli');
            $table->unsignedBigInteger('file_ukuran')->default(0);

            $table->boolean('is_public')->default(false);   // boleh dilihat publik?
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mou_pkl');
    }
};