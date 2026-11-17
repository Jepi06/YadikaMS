<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\PengampuMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        // Materi global milik kelas lain — read-only. Aturan cakupan ada di scope Materi::terlihatUntuk().
        $materiGlobal = Materi::terlihatUntuk($pengampuMapel)
            ->where('pengampu_mapel_id', '!=', $pengampuMapel->id)
            ->with('pengampuMapel.kelas')
            ->orderBy('urutan')
            ->orderBy('created_at')
            ->get();

        // Kelas lain yang diampu guru ini untuk mapel + tahun ajaran yang sama (pilihan cakupan "kelas pilihan").
        $kelasPilihan = $this->kelasPilihan($pengampuMapel);

        // Kelas tujuan per materi (untuk label & centang di panel cakupan).
        $kelasTujuan = DB::table('materi_kelas')
            ->join('kelas', 'kelas.id', '=', 'materi_kelas.kelas_id')
            ->whereIn('materi_kelas.materi_id', $materi->pluck('id'))
            ->orderBy('kelas.nama_kelas')
            ->get(['materi_kelas.materi_id', 'kelas.id as kelas_id', 'kelas.nama_kelas'])
            ->groupBy('materi_id');

        return view('lms.guru.materi', compact('pengampuMapel', 'materi', 'materiGlobal', 'kelasPilihan', 'kelasTujuan'));
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'pengantar' => ['nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
            'mode_akses' => ['required', 'in:bebas,berurutan,manual,tanggal'],
            'buka_pada' => ['nullable', 'date', 'required_if:mode_akses,tanggal'],
            'is_global' => ['nullable', 'boolean'],
        ]);

        $urutan = $pengampuMapel->materi()->max('urutan') + 1;

        Materi::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'pengantar' => $data['pengantar'] ?? null,
            'file_path' => $request->hasFile('file')
                ? $request->file('file')->store('materi', 'public')
                : null,
            'link_url' => $data['link_url'] ?? null,
            'urutan' => $urutan,
            'mode_akses' => $data['mode_akses'],
            'buka_pada' => $data['mode_akses'] === 'tanggal' ? $data['buka_pada'] : null,
            'is_global' => $request->boolean('is_global'),
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

    /** Buka/kunci manual — khusus materi mode "Dibuka Guru". */
    public function toggleBuka(Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);
        abort_unless($materi->mode_akses === 'manual', 422, 'Materi ini bukan mode "Dibuka Guru".');

        $materi->update(['dibuka_manual' => ! $materi->dibuka_manual]);

        return back()->with('status', $materi->dibuka_manual ? 'Materi dibuka untuk siswa.' : 'Materi dikunci lagi.');
    }

    /** Jadikan materi global (bisa dibuka kelas lain) atau khusus kelas ini. */
    public function toggleGlobal(Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $materi->update(['is_global' => ! $materi->is_global]);

        return back()->with('status', $materi->is_global
            ? 'Materi dibagikan ke kelas lain (global).'
            : 'Materi sekarang khusus kelas ini.');
    }

    /** Ubah/hapus link materi yang sudah ada. */
    public function updateLink(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $materi->update(['link_url' => $data['link_url'] ?? null]);

        return back()->with('status', 'Link materi diperbarui.');
    }

    /** Kelas lain yang diampu guru yang sama untuk mapel + tahun ajaran yang sama. */
    private function kelasPilihan(PengampuMapel $pm)
    {
        return PengampuMapel::with('kelas')
            ->where('guru_id', $pm->guru_id)
            ->where('mata_pelajaran_id', $pm->mata_pelajaran_id)
            ->where('tahun_ajaran', $pm->tahun_ajaran)
            ->where('kelas_id', '!=', $pm->kelas_id)
            ->get()
            ->pluck('kelas')
            ->filter()
            ->unique('id')
            ->sortBy('nama_kelas')
            ->values();
    }

    /**
     * Atur cakupan materi:
     *  khusus   → hanya kelas ini
     *  angkatan → semua kelas satu tingkat (mapel & tahun ajaran sama)
     *  kelas    → hanya kelas yang dipilih (harus kelas yang diampu guru ini untuk mapel yang sama)
     */
    public function updateCakupan(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'cakupan' => ['required', 'in:khusus,angkatan,kelas'],
            'kelas_ids' => ['nullable', 'array'],
            'kelas_ids.*' => ['integer'],
        ]);

        $ids = collect();
        if ($data['cakupan'] === 'kelas') {
            $boleh = $this->kelasPilihan($materi->pengampuMapel)->pluck('id');
            $ids = collect($data['kelas_ids'] ?? [])->map(fn ($v) => (int) $v)->unique()->values();

            if ($ids->isEmpty()) {
                throw ValidationException::withMessages(['kelas_ids' => 'Pilih minimal satu kelas.']);
            }
            if ($ids->diff($boleh)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'kelas_ids' => 'Ada kelas yang bukan kelas ampuan Anda untuk mapel ini.',
                ]);
            }
        }

        DB::transaction(function () use ($materi, $data, $ids) {
            $materi->update([
                'is_global' => $data['cakupan'] !== 'khusus',
                'cakupan_global' => $data['cakupan'] === 'kelas' ? 'kelas' : 'angkatan',
            ]);

            DB::table('materi_kelas')->where('materi_id', $materi->id)->delete();

            if ($ids->isNotEmpty()) {
                $now = now();
                DB::table('materi_kelas')->insert(
                    $ids->map(fn ($id) => [
                        'materi_id' => $materi->id,
                        'kelas_id' => $id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            }
        });

        return back()->with('status', match ($data['cakupan']) {
            'khusus' => 'Materi sekarang khusus kelas ini.',
            'angkatan' => 'Materi dibagikan ke satu angkatan.',
            default => 'Materi dibagikan ke kelas pilihan.',
        });
    }

    /** Ubah pengantar (popup) materi. */
    public function updatePengantar(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'pengantar' => ['nullable', 'string', 'max:5000'],
        ]);

        $materi->update(['pengantar' => $data['pengantar'] ?? null]);

        return back()->with('status', 'Pengantar materi diperbarui.');
    }

    /** Ganti mode akses materi yang sudah ada. */
    public function updateAkses(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'mode_akses' => ['required', 'in:bebas,berurutan,manual,tanggal'],
            'buka_pada' => ['nullable', 'date', 'required_if:mode_akses,tanggal'],
        ]);

        $materi->update([
            'mode_akses' => $data['mode_akses'],
            'buka_pada' => $data['mode_akses'] === 'tanggal' ? $data['buka_pada'] : null,
            'dibuka_manual' => $data['mode_akses'] === 'manual' ? $materi->dibuka_manual : false,
        ]);

        return back()->with('status', 'Pengaturan akses materi diperbarui.');
    }
}