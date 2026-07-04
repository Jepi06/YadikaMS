<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusanList = DB::table('jurusan')->pluck('id', 'kode');
        $tingkatList = ['X', 'XI', 'XII'];

        // Ambil user yang punya role wali_kelas di modul pkl (via user_role)
        $waliKelasIds = DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'pkl')
            ->where('roles.kode', 'wali_kelas')
            ->pluck('user_role.user_id')
            ->unique()
            ->values();

        $index = 0;

        foreach ($jurusanList as $kodeJurusan => $jurusanId) {
            foreach ($tingkatList as $tingkat) {
                for ($rombel = 1; $rombel <= 2; $rombel++) {
                    $waliKelasId = $waliKelasIds->isNotEmpty()
                        ? $waliKelasIds[$index % $waliKelasIds->count()]
                        : null;

                    DB::table('kelas')->insert([
                        'nama_kelas' => "{$tingkat} {$kodeJurusan} {$rombel}",
                        'tingkat' => $tingkat,
                        'jurusan_id' => $jurusanId,
                        'wali_kelas_id' => $waliKelasId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $index++;
                }
            }
        }
    }
}
