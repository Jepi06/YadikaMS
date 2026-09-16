<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Imports\KenaikanKelasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KenaikanKelasController extends Controller
{
   public function index()
{
    $kelas = \App\Models\Kelas::with('jurusan')
        ->withCount(['siswa' => fn($q) => $q->where('status', 'aktif')])
        ->orderBy('tingkat')
        ->orderBy('nama_kelas')
        ->get();

    return view('admin.kenaikan-kelas.index', compact('kelas'));
}
    public function downloadTemplate(Request $request)
{
    $request->validate([
        'kelas_id' => ['required', 'exists:kelas,id'],
    ]);

    return (new KenaikanKelasImport())->downloadTemplate((int) $request->kelas_id);
}
    /** Preview sebelum eksekusi (dry run) */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $import = (new KenaikanKelasImport())->setDryRun(true);
        Excel::import($import, $request->file('file'));

        // Simpan file sementara ke session untuk eksekusi
        $path = $request->file('file')->store('kenaikan-kelas-tmp', 'local');

        return view('admin.kenaikan-kelas.preview', [
            'preview'  => $import->getPreview(),
            'errors'   => $import->getErrors(),
            'skipped'  => $import->getSkipped(),
            'tmpPath'  => $path,
        ]);
    }

    /** Eksekusi kenaikan kelas setelah preview dikonfirmasi */
    public function eksekusi(Request $request)
    {
        $request->validate([
            'tmp_path' => ['required', 'string'],
        ]);

        $fullPath = storage_path('app/' . $request->tmp_path);

        if (!file_exists($fullPath)) {
            return redirect()->route('admin.kenaikan-kelas.index')
                ->withErrors(['file' => 'File sementara tidak ditemukan. Ulangi upload.']);
        }

        $import = (new KenaikanKelasImport())->setDryRun(false);
        Excel::import($import, $fullPath);

        // Hapus file sementara
        \Storage::disk('local')->delete($request->tmp_path);

        $msg = "Kenaikan kelas selesai: {$import->getUpdatedCount()} siswa naik kelas, {$import->getLulusCount()} siswa lulus/keluar.";

        return redirect()->route('admin.siswa.index')->with('success', $msg);
    }
}