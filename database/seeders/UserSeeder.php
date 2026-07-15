<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Contoh data guru/staff. Perhatikan array 'roles' pada beberapa
     * user berisi LEBIH DARI SATU kombinasi module+role — ini
     * menunjukkan bagaimana satu user bisa akses PKL, LMS, dan SPMB
     * sekaligus lewat tabel pivot user_role.
     *
     * Semua password default: "password"
     */
    public function run(): void
    {
        $superAdminId = DB::table('users')->where('email', 'admin@smk.sch.id')->value('id');

        $daftarUser = [
            [
                'name' => 'Hubin SMK',
                'email' => 'hubin@smk.sch.id',
                'roles' => [
                    ['pkl', 'admin'],
                ],
            ],
            [
                'name' => 'Guru BK',
                'email' => 'bk@smk.sch.id',
                'roles' => [
                    ['pkl', 'guru_bk'],
                ],
            ],
            [
                'name' => 'Kajur PPLG',
                'email' => 'kajur@smk.sch.id',
                'roles' => [
                    ['pkl', 'kepala_jurusan'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Wali Kelas PPLG',
                'email' => 'walas@smk.sch.id',
                'roles' => [
                    ['pkl', 'wali_kelas'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Budi Santoso, S.Kom',
                'email' => 'budi.santoso@smk.sch.id',
                'roles' => [
                    ['pkl', 'wali_kelas'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Siti Aminah, S.Pd',
                'email' => 'siti.aminah@smk.sch.id',
                'roles' => [
                    ['pkl', 'guru_bk'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'email' => 'ahmad.fauzi@smk.sch.id',
                'roles' => [
                    ['pkl', 'kesiswaan'],
                ],
            ],
            [
                'name' => 'Dedi Kurniawan, S.T',
                'email' => 'dedi.kurniawan@smk.sch.id',
                'roles' => [
                    ['pkl', 'kepala_jurusan'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Rina Wulandari, S.E',
                'email' => 'rina.wulandari@smk.sch.id',
                'roles' => [
                    ['ppdb', 'admin'],
                ],
            ],
            [
                'name' => 'Andi Prasetyo, S.Kom',
                'email' => 'andi.prasetyo@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                    ['pkl', 'wali_kelas'],
                ],
            ],
            [
                'name' => 'Maya Sari, S.Pd',
                'email' => 'maya.sari@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Yusuf Hidayat, S.Pd',
                'email' => 'yusuf.hidayat@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                    ['pkl', 'wali_kelas'],
                ],
            ],
            [
                'name' => 'Farah Nabila, S.Pd',
                'email' => 'farah.nabila@smk.sch.id',
                'roles' => [
                    ['ppdb', 'admin'],
                    ['lms', 'admin'],
                ],
            ],
            [
                'name' => 'Admin2',
                'email' => 'admin2@smk.sch.id',
                'roles' => [
                    ['pkl', 'admin'],
                    ['lms', 'admin'],
                    ['ppdb', 'admin'],
                ],
            ],
        ];

        foreach ($daftarUser as $data) {
            $userId = DB::table('users')->insertGetId([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($data['roles'] as [$moduleKode, $roleKode]) {
                $roleId = DB::table('roles')
                    ->join('modules', 'modules.id', '=', 'roles.module_id')
                    ->where('modules.kode', $moduleKode)
                    ->where('roles.kode', $roleKode)
                    ->value('roles.id');

                if (!$roleId) {
                    continue;
                }

                DB::table('user_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'assigned_by' => $superAdminId,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
