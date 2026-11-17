<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\KoreksiAbsensiHarian;
use App\Models\Lms\KoreksiAbsensiLog;
use App\Models\Lms\WaliKelasPeriode;
use App\Services\Lms\RekapAbsensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KoreksiAbsensiController extends Controller
{
    /** Wali kelas hanya boleh mengoreksi tanggal dalam N hari terakhir. */
    public const BATAS_HARI = 7;

    public function store(Request $request)
    {
        $user = Auth::guard('lms')->user();

        $data = $request->validate([
            'kelas_id' => 'required|integer',
            'siswa_id' => 'required|integer',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'tanggal' => 'required|date_format:Y-m-d',
            'status_koreksi' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'alasan' => 'required|string|min:10|max:500',
        ], [
            'alasan.required' => 'Alasan koreksi wajib diisi.',
            'alasan.min' => 'Alasan koreksi minimal 10 karakter.',
        ]);

        $kelas = $this->kelasWaliAtauAbort($user->id, $data);
        $this->pastikanDalamBatas($data['tanggal']);

        abort_unless(
            $kelas->siswa()->whereKey($data['siswa_id'])->exists(),
            403,
            'Siswa bukan anggota kelas ini.'
        );

        $sistem = RekapAbsensiService::statusSistem(
            $kelas, $data['tahun_ajaran'], $data['semester'], (int) $data['siswa_id'], $data['tanggal']
        );

        if ($sistem === null) {
            return back()->withErrors(['koreksi' => 'Tidak ada data presensi siswa ini pada tanggal tersebut.']);
        }
        if ($sistem === $data['status_koreksi']) {
            return back()->withErrors(['koreksi' => 'Status koreksi sama dengan status sistem (' . $sistem . '), tidak ada yang diubah.']);
        }

        $nama = $user->nama ?? ($user->name ?? '-');

        DB::transaction(function () use ($data, $user, $nama, $sistem) {
            $kunci = [
                'kelas_id' => $data['kelas_id'],
                'siswa_id' => $data['siswa_id'],
                'tanggal' => $data['tanggal'],
            ];
            $lama = KoreksiAbsensiHarian::where($kunci)->first();

            KoreksiAbsensiHarian::updateOrCreate($kunci, [
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
                'status_sistem' => $sistem,
                'status_koreksi' => $data['status_koreksi'],
                'alasan' => $data['alasan'],
                'diubah_oleh' => $user->id,
                'diubah_oleh_nama' => $nama,
            ]);

            KoreksiAbsensiLog::create($kunci + [
                'aksi' => $lama ? 'ubah' : 'set',
                'status_sistem' => $sistem,
                'status_lama' => $lama?->status_koreksi,
                'status_baru' => $data['status_koreksi'],
                'alasan' => $data['alasan'],
                'user_id' => $user->id,
                'user_nama' => $nama,
            ]);
        });

        return back()->with('success', 'Koreksi absensi disimpan.');
    }

    public function destroy(Request $request, int $id)
    {
        $user = Auth::guard('lms')->user();
        $koreksi = KoreksiAbsensiHarian::findOrFail($id);

        $this->kelasWaliAtauAbort($user->id, [
            'kelas_id' => $koreksi->kelas_id,
            'tahun_ajaran' => $koreksi->tahun_ajaran,
            'semester' => $koreksi->semester,
        ]);
        $this->pastikanDalamBatas(Carbon::parse($koreksi->tanggal)->format('Y-m-d'));

        $nama = $user->nama ?? ($user->name ?? '-');

        DB::transaction(function () use ($koreksi, $user, $nama) {
            KoreksiAbsensiLog::create([
                'kelas_id' => $koreksi->kelas_id,
                'siswa_id' => $koreksi->siswa_id,
                'tanggal' => $koreksi->tanggal,
                'aksi' => 'batal',
                'status_sistem' => $koreksi->status_sistem,
                'status_lama' => $koreksi->status_koreksi,
                'status_baru' => null,
                'alasan' => $koreksi->alasan,
                'user_id' => $user->id,
                'user_nama' => $nama,
            ]);
            $koreksi->delete();
        });

        return back()->with('success', 'Koreksi dibatalkan, status kembali ke hitungan sistem.');
    }

    /** Pastikan user adalah wali kelas untuk kelas + periode ini. */
    private function kelasWaliAtauAbort(int $userId, array $data): Kelas
    {
        $adalahWali = WaliKelasPeriode::where('user_id', $userId)
            ->where('kelas_id', $data['kelas_id'])
            ->where('tahun_ajaran', $data['tahun_ajaran'])
            ->where('semester', $data['semester'])
            ->exists();

        abort_unless($adalahWali, 403, 'Anda bukan wali kelas untuk kelas dan periode ini.');

        return Kelas::findOrFail($data['kelas_id']);
    }

    private function pastikanDalamBatas(string $tanggal): void
    {
        $batas = now()->subDays(self::BATAS_HARI)->toDateString();
        abort_if($tanggal > now()->toDateString(), 422, 'Tidak bisa mengoreksi tanggal yang belum terjadi.');
        abort_if($tanggal < $batas, 403, 'Koreksi hanya boleh dilakukan maksimal ' . self::BATAS_HARI . ' hari setelah tanggal presensi. Hubungi admin.');
    }
}