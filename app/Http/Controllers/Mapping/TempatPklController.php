<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\MouPkl;
use App\Models\Mapping\TempatPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TempatPklController extends Controller
{
    public function index(Request $request)
    {
        $query = TempatPkl::withCount(['penempatanPkl', 'mou']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_tempat', 'like', "%{$request->search}%")
                    ->orWhere('bidang_usaha', 'like', "%{$request->search}%");
            });
        }

        $tempat = $query->orderBy('nama_tempat')->paginate(15)->withQueryString();

        return view('Mapping.tempat.index', compact('tempat'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'alamat' => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'kuota_maksimal' => 'nullable|integer|min:1', // kosong = tidak dibatasi
        ]);

        TempatPkl::create($data);

        return back()->with('success', 'Tempat PKL berhasil ditambahkan.');
    }

    public function update(Request $request, TempatPkl $tempat)
    {
        $data = $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'alamat' => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'kuota_maksimal' => 'nullable|integer|min:1',
        ]);

        // Cegah admin set kuota lebih kecil dari jumlah yang sudah terisi
        // (misal sudah 7 orang aktif, tidak boleh diset jadi 3).
        if (
            $data['kuota_maksimal'] !== null &&
            $data['kuota_maksimal'] < $tempat->jumlahTerisi()
        ) {
            return back()->with(
                'error',
                "Kuota tidak bisa diset lebih kecil dari jumlah siswa yang sudah ditempatkan ({$tempat->jumlahTerisi()} orang)."
            );
        }

        $tempat->update($data);

        return back()->with('success', 'Data tempat PKL berhasil diperbarui.');
    }

    public function destroy(TempatPkl $tempat)
    {
        $tempat->delete(); // berkas MOU ikut dibersihkan lewat hook di model

        return back()->with('success', 'Tempat PKL berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    //  DETAIL TEMPAT + ARSIP MOU
    // ══════════════════════════════════════════════════════════════

    /** Halaman detail tempat PKL + daftar & upload arsip MOU. */
    public function show(TempatPkl $tempat)
    {
        $this->pastikanAdmin();

        $tempat->loadCount('penempatanPkl');
        $mou = $tempat->mou()->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();

        return view('Mapping.tempat.show', compact('tempat', 'mou'));
    }

    /** Upload 1 arsip MOU untuk tempat ini. */
    public function storeMou(Request $request, TempatPkl $tempat)
    {
        $this->pastikanAdmin();

        $request->validate([
            'judul' => 'nullable|string|max:255',
            'nomor_mou' => 'nullable|string|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10 MB
        ], [
            'file.required' => 'Pilih berkas MOU yang akan diunggah.',
            'file.mimes' => 'Berkas harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
        ]);

        $file = $request->file('file');
        $namaAsli = $file->getClientOriginalName();
        $ukuran = $file->getSize();

        // Disk 'local' = privat (tidak bisa dibuka lewat URL langsung).
        $path = $file->store('mou-pkl', 'local');

        $tempat->mou()->create([
            'judul' => $request->input('judul'),
            'nomor_mou' => $request->input('nomor_mou'),
            'tanggal_mulai' => $request->input('tanggal_mulai'),
            'tanggal_berakhir' => $request->input('tanggal_berakhir'),
            'keterangan' => $request->input('keterangan'),
            'file_path' => $path,
            'file_nama_asli' => $namaAsli,
            'file_ukuran' => $ukuran,
            'is_public' => $request->boolean('is_public'),
            'uploaded_by' => Auth::guard('pkl')->id(),
        ]);

        return back()->with('success', 'Arsip MOU berhasil diunggah.');
    }

    /** Saklar: berkas MOU ini boleh dilihat publik atau tidak. */
    public function toggleMouPublik(Request $request, MouPkl $mou)
    {
        $this->pastikanAdmin();

        $mou->update(['is_public' => $request->boolean('is_public')]);

        return back()->with('success', $mou->is_public
            ? 'Berkas MOU sekarang BISA dilihat publik.'
            : 'Berkas MOU sekarang PRIVAT (tidak tampil ke publik).');
    }

    public function destroyMou(MouPkl $mou)
    {
        $this->pastikanAdmin();

        Storage::disk('local')->delete($mou->file_path);
        $mou->delete();

        return back()->with('success', 'Arsip MOU dihapus.');
    }

    /** Admin membuka berkas MOU (privat maupun publik). */
    public function berkasMou(MouPkl $mou)
    {
        $this->pastikanAdmin();

        abort_unless(Storage::disk('local')->exists($mou->file_path), 404, 'Berkas tidak ditemukan.');

        return Storage::disk('local')->response($mou->file_path, $mou->file_nama_asli);
    }

    /** Hanya admin PKL (atau super admin) yang boleh mengelola arsip MOU. */
    private function pastikanAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('pkl')->user();

        abort_unless($user && ($user->isAdminAtauHubin() || $user->isSuperAdmin()), 403);
    }
}