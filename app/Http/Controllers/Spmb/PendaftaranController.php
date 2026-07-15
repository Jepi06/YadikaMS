<?php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\SPMB\Jurusan;
use App\Models\SPMB\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    /**
     * Form pendaftaran mandiri untuk calon siswa (route: spmb.daftar.create).
     */
    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama')->get(['id', 'nama', 'kuota']);

        return view('spmb.public.create', compact('jurusanList'));
    }

    /**
     * Simpan pendaftaran mandiri (route: spmb.daftar.store).
     *
     * PENTING: siswa TIDAK boleh mengisi nominal pembayaran maupun status.
     * Kedua kolom tsb sengaja tidak ada di $rules maupun di $data yang
     * disimpan — nilai apa pun yang dikirim untuk field itu akan diabaikan.
     * Nominal & status hanya bisa diisi/diubah admin lewat
     * Spmb\Admin\PendaftarController (mis. pendaftar.nominal, pendaftar.status).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'asal_sekolah' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            // 'nominal' / 'status' SENGAJA TIDAK ADA di sini.
        ]);

        // Jaga-jaga: buang paksa field sensitif bila ada yang menyisipkannya
        // lewat request (mis. lewat devtools / curl).
        unset($data['nominal'], $data['nominal_pembayaran'], $data['biaya'], $data['status']);

        $data['no_pendaftaran'] = $this->generateNoPendaftaran();
        $data['status'] = 'menunggu'; // status awal, hanya admin yang bisa mengubah

        $pendaftar = Pendaftar::create($data);

        return redirect()
            ->route('spmb.daftar.success', $pendaftar->no_pendaftaran)
            ->with('success', 'Pendaftaran berhasil dikirim!');
    }

    /**
     * Halaman sukses setelah mendaftar (route: spmb.daftar.success).
     * Route-model-binding via no_pendaftaran: {pendaftar:no_pendaftaran}.
     */
    public function success(Pendaftar $pendaftar)
    {
        return view('spmb.public.sukses', compact('pendaftar'));
    }

    /**
     * Buat nomor pendaftaran unik. Sesuaikan format dengan kebutuhan sekolah.
     */
    protected function generateNoPendaftaran(): string
    {
        do {
            $noPendaftaran = 'PPDB-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
        } while (Pendaftar::where('no_pendaftaran', $noPendaftaran)->exists());

        return $noPendaftaran;
    }
}
