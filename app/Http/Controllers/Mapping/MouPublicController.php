<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\MouPkl;
use App\Models\Mapping\TempatPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Halaman PUBLIK (tanpa login): daftar mitra industri yang sudah
 * menjalin MOU dengan sekolah + akses berkas MOU yang diizinkan publik.
 */
class MouPublicController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status'); // 'aktif' | 'berakhir' | null
        $hariIni = now()->toDateString();

        $tempat = TempatPkl::query()
            ->whereHas('mou', function ($m) use ($status, $hariIni) {
                if ($status === 'aktif') {
                    $m->where(fn($q) => $q->whereNull('tanggal_berakhir')
                        ->orWhereDate('tanggal_berakhir', '>=', $hariIni));
                } elseif ($status === 'berakhir') {
                    $m->whereDate('tanggal_berakhir', '<', $hariIni);
                }
            })
            ->with(['mou' => fn($m) => $m->orderByDesc('tanggal_mulai')->orderByDesc('id')])
            ->when($search !== '', fn($q) => $q->where(fn($s) => $s
                ->where('nama_tempat', 'like', "%{$search}%")
                ->orWhere('bidang_usaha', 'like', "%{$search}%")))
            ->orderBy('nama_tempat')
            ->paginate(12)
            ->withQueryString();

        $totalMitra = TempatPkl::whereHas('mou')->count();
        $totalMou = MouPkl::count();
        $totalPublik = MouPkl::where('is_public', true)->count();

        return view('Mapping.public.mou', compact(
            'tempat', 'search', 'status', 'totalMitra', 'totalMou', 'totalPublik'
        ));
    }

    /** Berkas MOU — HANYA kalau admin menandainya publik. */
    public function berkas(MouPkl $mou)
    {
        abort_unless($mou->is_public, 404);
        abort_unless(Storage::disk('local')->exists($mou->file_path), 404, 'Berkas tidak ditemukan.');

        return Storage::disk('local')->response($mou->file_path, $mou->file_nama_asli);
    }
}