<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menambah role "Guru Piket" di modul LMS agar muncul di halaman
 * Admin → Pengguna → Kelola Akses. Aman dijalankan berulang (updateOrInsert),
 * jadi tidak masalah kalau migrasi poin siswa sudah membuatnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        $moduleId = DB::table('modules')->where('kode', 'lms')->value('id');
        if (! $moduleId) {
            return;
        }

        $now = now();
        DB::table('roles')->updateOrInsert(
            ['module_id' => $moduleId, 'kode' => 'guru_piket'],
            ['nama' => 'Guru Piket', 'updated_at' => $now, 'created_at' => $now]
        );
    }

    public function down(): void
    {
        $moduleId = DB::table('modules')->where('kode', 'lms')->value('id');
        $roleId = DB::table('roles')->where('module_id', $moduleId)->where('kode', 'guru_piket')->value('id');
        if ($roleId) {
            DB::table('user_role')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};