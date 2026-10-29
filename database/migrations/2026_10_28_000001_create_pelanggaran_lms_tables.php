<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelanggaran_lms', function (Blueprint $t) {
            $t->id();
            $t->enum('tipe', ['pelanggaran', 'prestasi'])->default('pelanggaran');
            $t->string('kategori', 50);
            $t->string('nama');
            $t->unsignedSmallInteger('poin');
            $t->unsignedSmallInteger('urutan')->default(0);
            $t->boolean('aktif')->default(true);
            $t->timestamps();
        });

        Schema::create('pelanggaran_siswa_lms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            // snapshot kelas saat kejadian (siswa bisa naik kelas)
            $t->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $t->foreignId('jenis_pelanggaran_id')->nullable()->constrained('jenis_pelanggaran_lms')->nullOnDelete();
            // snapshot nama & poin → riwayat tidak berubah kalau master poin diedit
            $t->enum('tipe', ['pelanggaran', 'prestasi'])->default('pelanggaran');
            $t->string('kategori', 50)->nullable();
            $t->string('nama_pelanggaran');
            $t->unsignedSmallInteger('poin')->default(0);
            $t->date('tanggal');
            $t->text('catatan')->nullable();
            $t->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->string('peran_pencatat', 30)->nullable();
            $t->string('tahun_ajaran', 9);
            $t->enum('semester', ['Ganjil', 'Genap']);
            $t->timestamps();

            $t->index(['siswa_id', 'tanggal']);
        });

        // ── Seed master pelanggaran (sesuai tata tertib: Bab IV) ──
        $data = [
            'Kehadiran' => [
                ['Tidak masuk sekolah tanpa keterangan', 25],
                ['Tidak hadir dalam kegiatan ekstrakurikuler tanpa keterangan', 25],
                ['Terlambat hadir di sekolah', 20],
                ['Meninggalkan sekolah tanpa izin', 50],
                ['Tidak mengikuti hari besar sekolah', 50],
            ],
            'Kerapihan' => [
                ['Tidak memakai seragam sekolah secara yang lengkap', 25],
                ['Mengubah bentuk seragam/serta memendekkan rok diatas lutut', 20],
                ['Mewarnai atau memanjangkan rambut', 50],
                ['Memakai aksesoris/perhiasan berlebihan', 25],
                ['Make-up berlebihan', 25],
                ['Kuku panjang atau dicat', 20],
            ],
            'Ketertiban dan Keamanan' => [
                ['Berkelahi', 300],
                ['Mencuri', 300],
                ['Melawan guru', 300],
                ['Menyontek/memberi sontekan', 200],
                ['Merokok di lingkungan sekolah', 200],
                ['Membawa buku bacaan, film, media elektronik lain atau memiliki gambar/cerita/VCD porno ke sekolah yang tidak ada hubungannya', 200],
                ['Membawa senjata tajam, senjata api', 300],
                ['Menyalahgunakan uang sekolah', 200],
                ['Bicara kasar/berkata kotor jorok di lingkungan sekolah', 50],
                ['Tidak jujur', 50],
                ['Berpindah-pindah tempat duduk sewaktu belajar', 25],
                ['Tidak mengerjakan PR/Tugas sekolah', 25],
                ['Tidak membawa alat-alat/buku pelajaran yang sedang dibelajarkan', 25],
                ['Perilaku tidak sopan pada orang lain', 25],
                ['Mengganggu proses pembelajaran yang sedang berlangsung', 25],
                ['Tidak melaksanakan tugas piket dengan baik dan benar', 25],
                ['Menyakiti hati orang lain', 25],
                ['Berjalan-jalan pada saat jam pelajaran/pergantian pelajaran', 20],
                ['Memakai/mengedarkan obat terlarang dan sejenisnya', 500],
                ['Terlambat masuk kelas', 25],
                ['Melanggar norma-norma yang berlaku di masyarakat seperti terlibat dalam tawuran, hamil diluar nikah dan tindak kriminal', 500],
            ],
            'Keindahan' => [
                ['Merusak sarana dan prasarana sekolah', 200],
                ['Membawa handphone berkamera', 200],
                ['Mencoret-coret meja, tembok', 100],
                ['Merusak tanaman', 100],
                ['Membuang sampah tidak pada tempatnya', 25],
                ['Makan makanan di dalam kelas', 25],
            ],
        ];

        $now = now();
        $urutan = 0;
        foreach ($data as $kategori => $items) {
            foreach ($items as [$nama, $poin]) {
                DB::table('jenis_pelanggaran_lms')->insert([
                    'tipe' => 'pelanggaran', 'kategori' => $kategori, 'nama' => $nama, 'poin' => $poin,
                    'urutan' => ++$urutan, 'aktif' => true,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── Seed prestasi (USULAN nilai — silakan ubah di tabel jenis_pelanggaran_lms) ──
        $prestasi = [
            'Akademik & Lomba' => [
                ['Juara lomba tingkat sekolah', 25],
                ['Juara lomba tingkat kecamatan/kabupaten', 50],
                ['Juara lomba tingkat provinsi', 100],
                ['Juara lomba tingkat nasional', 150],
                ['Juara lomba tingkat internasional', 200],
                ['Peringkat 1-3 di kelas', 50],
                ['Mewakili sekolah dalam kegiatan/kompetisi', 50],
            ],
            'Kedisiplinan & Karakter' => [
                ['Kehadiran 100% selama satu bulan', 25],
                ['Menjadi teladan / membantu kegiatan sekolah', 20],
                ['Menemukan & mengembalikan barang milik orang lain', 20],
            ],
        ];
        foreach ($prestasi as $kategori => $items) {
            foreach ($items as [$nama, $poin]) {
                DB::table('jenis_pelanggaran_lms')->insert([
                    'tipe' => 'prestasi', 'kategori' => $kategori, 'nama' => $nama, 'poin' => $poin,
                    'urutan' => ++$urutan, 'aktif' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── Role baru: Guru Piket (modul LMS) ──
        $moduleId = DB::table('modules')->where('kode', 'lms')->value('id');
        if ($moduleId) {
            DB::table('roles')->updateOrInsert(
                ['module_id' => $moduleId, 'kode' => 'guru_piket'],
                ['nama' => 'Guru Piket', 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa_lms');
        Schema::dropIfExists('jenis_pelanggaran_lms');

        $moduleId = DB::table('modules')->where('kode', 'lms')->value('id');
        $roleId = DB::table('roles')->where('module_id', $moduleId)->where('kode', 'guru_piket')->value('id');
        if ($roleId) {
            DB::table('user_role')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
