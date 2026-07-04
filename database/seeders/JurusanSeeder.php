<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            ['nama' => 'Perhotelan', 'kode' => 'HTL'],
            ['nama' => 'Pengembangan Perangkat Lunak Dan Gim', 'kode' => 'PPLG'],
            ['nama' => 'Akuntansi', 'kode' => 'AK'],
        ];

        foreach ($jurusan as $j) {
            DB::table('jurusan')->insert(array_merge($j, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
