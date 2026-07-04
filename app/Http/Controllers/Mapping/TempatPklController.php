<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\TempatPkl;
use Illuminate\Http\Request;

class TempatPklController extends Controller
{
    public function index(Request $request)
    {
        $query = TempatPkl::withCount('penempatanPkl');

        if ($request->filled('search')) {
            // PERBAIKAN: orWhere sebelumnya tidak dibungkus closure, sehingga
            // kalau nanti ditambah filter lain (mis. where('bidang_usaha', ...))
            // orWhere ini akan "lepas" dari filter search dan membuat hasilnya salah.
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
            'nama_tempat'  => 'required|string|max:255',
            'alamat'       => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak'  => 'nullable|string|max:255',
            'no_telp'      => 'nullable|string|max:20',
        ]);

        TempatPkl::create($data);

        return back()->with('success', 'Tempat PKL berhasil ditambahkan.');
    }

    public function update(Request $request, TempatPkl $tempat)
    {
        $data = $request->validate([
            'nama_tempat'  => 'required|string|max:255',
            'alamat'       => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak'  => 'nullable|string|max:255',
            'no_telp'      => 'nullable|string|max:20',
        ]);

        $tempat->update($data);

        return back()->with('success', 'Data tempat PKL berhasil diperbarui.');
    }

    public function destroy(TempatPkl $tempat)
    {
        $tempat->delete();

        return back()->with('success', 'Tempat PKL berhasil dihapus.');
    }
}
