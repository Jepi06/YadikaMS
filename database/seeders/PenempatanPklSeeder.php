<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenempatanPklSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya siswa kelas XI/XII yang jadi kandidat PKL
        $siswaKandidat = DB::table('siswa')
            ->join('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->whereIn('kelas.tingkat', ['XI', 'XII'])
            ->pluck('siswa.id');

        if ($siswaKandidat->isEmpty()) {
            return;
        }

        $tempatPklIds = DB::table('tempat_pkl')->pluck('id');
        $guruPembimbingIds = DB::table('guru_pembimbing')->pluck('id');

        $waliKelasId = $this->userIdByRole('pkl', 'wali_kelas');
        $guruBkId = $this->userIdByRole('pkl', 'guru_bk');
        $kesiswaanId = $this->userIdByRole('pkl', 'kesiswaan');
        $kajurId = $this->userIdByRole('pkl', 'kepala_jurusan');

        // Ambil 60% dari kandidat sebagai contoh yang sudah diajukan PKL
        $jumlahDipilih = (int) ceil($siswaKandidat->count() * 0.6);
        $terpilih = $siswaKandidat->random($jumlahDipilih);
        $terpilih = $terpilih instanceof \Illuminate\Support\Collection ? $terpilih : collect([$terpilih]);

        foreach ($terpilih as $siswaId) {
            $mulai = Carbon::create(2024, 7, 15);
            $selesai = (clone $mulai)->addMonths(3);

            $skenario = fake()->randomElement(['draft', 'sebagian_approve', 'full_approve', 'ada_reject']);

            $row = [
                'siswa_id' => $siswaId,
                'tempat_pkl_id' => $tempatPklIds->random(),
                'guru_pembimbing_id' => $guruPembimbingIds->random(),
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'tahun_ajaran' => '2024/2025',
                'keterangan' => null,
                'status' => 'draft',
                'status_wali_kelas' => 'pending',
                'status_guru_bk' => 'pending',
                'status_kesiswaan' => 'pending',
                'status_kepala_jurusan' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($skenario === 'sebagian_approve') {
                $row['status'] = 'diajukan';
                $row['status_wali_kelas'] = 'approved';
                $row['approved_by_wali_kelas'] = $waliKelasId;
                $row['approved_at_wali_kelas'] = now();
                $row['status_guru_bk'] = 'approved';
                $row['approved_by_guru_bk'] = $guruBkId;
                $row['approved_at_guru_bk'] = now();
            } elseif ($skenario === 'full_approve') {
                $row['status'] = 'approved';
                $row['status_wali_kelas'] = 'approved';
                $row['approved_by_wali_kelas'] = $waliKelasId;
                $row['approved_at_wali_kelas'] = now();
                $row['status_guru_bk'] = 'approved';
                $row['approved_by_guru_bk'] = $guruBkId;
                $row['approved_at_guru_bk'] = now();
                $row['status_kesiswaan'] = 'approved';
                $row['approved_by_kesiswaan'] = $kesiswaanId;
                $row['approved_at_kesiswaan'] = now();
                $row['status_kepala_jurusan'] = 'approved';
                $row['approved_by_kepala_jurusan'] = $kajurId;
                $row['approved_at_kepala_jurusan'] = now();
            } elseif ($skenario === 'ada_reject') {
                $row['status'] = 'rejected';
                $row['status_wali_kelas'] = 'approved';
                $row['approved_by_wali_kelas'] = $waliKelasId;
                $row['approved_at_wali_kelas'] = now();
                $row['status_guru_bk'] = 'rejected';
                $row['approved_by_guru_bk'] = $guruBkId;
                $row['approved_at_guru_bk'] = now();
                $row['catatan_guru_bk'] = 'Perlu perbaikan surat pengantar dari orang tua.';
            }

            DB::table('penempatan_pkl')->insert($row);
        }
    }

    private function userIdByRole(string $moduleKode, string $roleKode): ?int
    {
        return DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', $moduleKode)
            ->where('roles.kode', $roleKode)
            ->value('user_role.user_id');
    }
}
