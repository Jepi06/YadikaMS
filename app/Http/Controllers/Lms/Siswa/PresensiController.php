<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    /**
     * Dibuka lewat scan QR (link berisi token). Siswa harus sudah login
     * (guard lms) di device yang dipakai scan — kalau belum, middleware
     * auth.lms otomatis lempar ke halaman login dulu.
     */
    public function scan(string $token)
    {
        $siswa = Auth::guard('lms')->user()->siswa;

        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa. Hubungi admin.');

        $sesi = SesiPresensi::where('token', $token)
            ->with('pengampuMapel.mataPelajaran')
            ->first();

        if (! $sesi) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'QR tidak dikenali atau sudah tidak berlaku.',
            ]);
        }

        if (! is_null($sesi->ditutup_at)) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'Sesi presensi ini sudah ditutup oleh guru.',
            ]);
        }

        if ($sesi->pengampuMapel->kelas_id !== $siswa->kelas_id) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'QR ini bukan untuk kelas Anda.',
            ]);
        }

        $presensi = PresensiLms::updateOrCreate(
            [
                'pengampu_mapel_id' => $sesi->pengampu_mapel_id,
                'siswa_id' => $siswa->id,
                'tanggal' => $sesi->tanggal,
            ],
            [
                'status' => 'Hadir',
                'sumber' => 'barcode',
                'sesi_presensi_id' => $sesi->id,
            ]
        );

        return view('lms.siswa.presensi-scan', [
            'berhasil' => true,
            'pesan' => 'Anda tercatat HADIR pada mata pelajaran '
                . ($sesi->pengampuMapel->mataPelajaran->nama ?? '-') . '.',
            'waktu' => $presensi->updated_at,
        ]);
    }

    public function riwayat()
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');

        $riwayat = PresensiLms::with('pengampuMapel.mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->paginate(20);

        return view('lms.siswa.presensi-riwayat', compact('riwayat'));
    }
}
