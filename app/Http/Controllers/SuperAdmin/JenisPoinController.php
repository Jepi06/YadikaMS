<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\JenisPelanggaran;
use App\Models\Lms\PelanggaranSiswa;
use Illuminate\Http\Request;

/**
 * Super Admin: kelola daftar pelanggaran (poin berkurang) dan prestasi (poin bertambah).
 * Perubahan di sini TIDAK mengubah catatan yang sudah tersimpan — setiap catatan
 * menyimpan nama & poin saat kejadian.
 */
class JenisPoinController extends Controller
{
    public function index(Request $request)
    {
        $tipe = in_array($request->query('tipe'), ['pelanggaran', 'prestasi'], true) ? $request->query('tipe') : null;
        $q    = trim((string) $request->query('q'));

        $items = JenisPelanggaran::query()
            ->when($tipe, fn($x) => $x->where('tipe', $tipe))
            ->when($q, fn($x) => $x->where(fn($w) => $w
                ->where('nama', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%")))
            ->orderBy('tipe')->orderBy('urutan')->orderBy('id')
            ->get();

        $jumlah = JenisPelanggaran::selectRaw('tipe, COUNT(*) as n')->groupBy('tipe')->pluck('n', 'tipe');

        $dipakai = PelanggaranSiswa::whereNotNull('jenis_pelanggaran_id')
            ->selectRaw('jenis_pelanggaran_id, COUNT(*) as n')
            ->groupBy('jenis_pelanggaran_id')
            ->pluck('n', 'jenis_pelanggaran_id');

        return view('admin.poin.index', compact('items', 'tipe', 'q', 'jumlah', 'dipakai'));
    }

    public function create(Request $request)
    {
        $jenis = new JenisPelanggaran([
            'tipe' => $request->query('tipe') === 'prestasi' ? 'prestasi' : 'pelanggaran',
            'aktif' => true,
        ]);

        return view('admin.poin.form', ['jenis' => $jenis, 'kategoriList' => $this->kategoriList()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['urutan'] = $data['urutan'] ?? ((int) JenisPelanggaran::max('urutan') + 1);

        JenisPelanggaran::create($data);

        return redirect()->route('admin.poin.index', ['tipe' => $data['tipe']])
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(JenisPelanggaran $jenis)
    {
        return view('admin.poin.form', ['jenis' => $jenis, 'kategoriList' => $this->kategoriList()]);
    }

    public function update(Request $request, JenisPelanggaran $jenis)
    {
        $data = $this->validasi($request);
        $data['urutan'] = $data['urutan'] ?? $jenis->urutan;

        $jenis->update($data);

        return redirect()->route('admin.poin.index', ['tipe' => $jenis->tipe])
            ->with('success', 'Data berhasil diperbarui. Catatan lama tidak berubah.');
    }

    public function toggle(JenisPelanggaran $jenis)
    {
        $jenis->update(['aktif' => ! $jenis->aktif]);

        return back()->with('success', '"' . $jenis->nama . '" ' . ($jenis->aktif ? 'diaktifkan' : 'dinonaktifkan') . '.');
    }

    public function destroy(JenisPelanggaran $jenis)
    {
        $nama = $jenis->nama;
        $jenis->delete(); // catatan siswa tetap utuh (nama & poin tersimpan di catatan)

        return back()->with('success', "\"{$nama}\" dihapus. Riwayat siswa yang sudah tercatat tetap aman.");
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'tipe'     => ['required', 'in:pelanggaran,prestasi'],
            'kategori' => ['required', 'string', 'max:50'],
            'nama'     => ['required', 'string', 'max:255'],
            'poin'     => ['required', 'integer', 'min:1', 'max:500'],
            'urutan'   => ['nullable', 'integer', 'min:0', 'max:65000'],
        ], [
            'poin.min' => 'Poin minimal 1.',
            'poin.max' => 'Poin maksimal 500.',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }

    private function kategoriList()
    {
        return JenisPelanggaran::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
    }
}
