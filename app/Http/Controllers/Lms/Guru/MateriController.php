<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\PengampuMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas');
        $materi = $pengampuMapel->materi()->orderBy('urutan')->orderBy('created_at')->get();

        return view('lms.guru.materi', compact('pengampuMapel', 'materi'));
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB
        ]);

        $urutan = $pengampuMapel->materi()->max('urutan') + 1;

        Materi::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $request->hasFile('file')
                ? $request->file('file')->store('materi', 'public')
                : null,
            'urutan' => $urutan,
        ]);

        return back()->with('status', 'Materi berhasil ditambahkan.');
    }

    public function destroy(Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('status', 'Materi dihapus.');
    }
}
