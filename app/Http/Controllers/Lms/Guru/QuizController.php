<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PercobaanQuiz;
use App\Models\Lms\Quiz;
use App\Models\Siswa;
use App\Services\Lms\QuizService;
use App\Services\Lms\QuizTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuizController extends Controller
{
    private function authPengampu(PengampuMapel $pm): void
    {
        abort_unless($pm->guru_id === Auth::guard('lms')->id(), 403, 'Anda bukan pengampu kelas ini.');
    }

    private function authQuiz(Quiz $quiz): PengampuMapel
    {
        $pm = $quiz->pengampuMapel;
        abort_unless($pm && $pm->guru_id === Auth::guard('lms')->id(), 403, 'Anda bukan pengampu kelas ini.');

        return $pm;
    }

    private function aturan(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'durasi_menit' => ['required', 'integer', 'min:1', 'max:300'],
            'mulai_pada' => ['nullable', 'date'],
            'selesai_pada' => ['nullable', 'date', 'after_or_equal:mulai_pada'],
            'maks_percobaan' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    private function dataQuiz(Request $request, array $valid): array
    {
        return $valid + [
            'acak_soal' => $request->boolean('acak_soal'),
            'tampil_pembahasan' => $request->boolean('tampil_pembahasan'),
        ];
    }

    // ───────── Template ─────────

    public function template(string $format)
    {
        abort_unless(in_array($format, ['xlsx', 'docx'], true), 404);

        if ($format === 'xlsx') {
            $writer = new Xlsx(QuizTemplateService::buatXlsx());

            return response()->streamDownload(fn() => $writer->save('php://output'), 'Template-Quiz.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return response()->download(QuizTemplateService::buatDocx(), 'Template-Quiz.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    // ───────── Daftar & buat ─────────

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authPengampu($pengampuMapel);
        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $quiz = Quiz::where('pengampu_mapel_id', $pengampuMapel->id)->withCount('soal')->orderByDesc('id')->get();

        QuizService::finalisasiKadaluarsa($quiz->pluck('id')->all());

        $peserta = PercobaanQuiz::whereIn('quiz_id', $quiz->pluck('id'))->where('status', 'selesai')
            ->groupBy('quiz_id')->selectRaw('quiz_id, COUNT(DISTINCT siswa_id) as n')->pluck('n', 'quiz_id');

        return view('lms.guru.quiz.index', [
            'pengampuMapel' => $pengampuMapel,
            'quiz' => $quiz,
            'peserta' => $peserta,
            'jumlahSiswa' => $pengampuMapel->kelas->siswa->count(),
        ]);
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authPengampu($pengampuMapel);

        $valid = $request->validate($this->aturan() + [
            'file' => ['required', 'file', 'max:5120', 'extensions:xlsx,xls,docx'],
        ], [
            'file.required' => 'Unggah file soal (template Excel atau Word).',
            'file.extensions' => 'File harus berformat .xlsx, .xls, atau .docx.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $file = $request->file('file');
        [$soal, $error] = QuizTemplateService::baca($file->getRealPath(), $file->getClientOriginalExtension());

        if ($error) {
            return back()->withInput()->with('import_errors', $error);
        }

        $data = $this->dataQuiz($request, collect($valid)->except('file')->all());

        $quiz = DB::transaction(function () use ($pengampuMapel, $data, $soal) {
            $quiz = Quiz::create($data + ['pengampu_mapel_id' => $pengampuMapel->id, 'status' => 'draft']);
            foreach ($soal as $i => $s) {
                $quiz->soal()->create($s + ['urutan' => $i + 1]);
            }

            return $quiz;
        });

        return redirect()->route('lms.guru.quiz.show', $quiz)
            ->with('status', count($soal) . ' soal berhasil diimpor. Periksa soal di bawah, lalu klik "Terbitkan" agar siswa bisa mengerjakan.');
    }

    // ───────── Detail, pengaturan, hasil ─────────

    public function show(Quiz $quiz)
    {
        $pm = $this->authQuiz($quiz);
        $pm->load('mataPelajaran', 'kelas.siswa');

        QuizService::finalisasiKadaluarsa([$quiz->id]);

        $quiz->load('soal');

        $percobaan = PercobaanQuiz::where('quiz_id', $quiz->id)->get()->groupBy('siswa_id');

        $hasil = $pm->kelas->siswa->sortBy(fn($s) => strtolower($s->nama ?? ''))->values()->map(function ($s) use ($percobaan) {
            $p = $percobaan->get($s->id, collect());
            $selesai = $p->where('status', 'selesai');

            return (object) [
                'siswa' => $s,
                'jumlah' => $p->count(),
                'berjalan' => $p->where('status', 'berjalan')->isNotEmpty(),
                'nilai' => $selesai->isNotEmpty() ? (float) $selesai->max('nilai') : null,
                'benar' => $selesai->sortByDesc('nilai')->first()->benar ?? null,
            ];
        });

        $nilaiAda = $hasil->whereNotNull('nilai');

        return view('lms.guru.quiz.show', [
            'quiz' => $quiz,
            'pengampuMapel' => $pm,
            'hasil' => $hasil,
            'sudahAdaPercobaan' => $percobaan->isNotEmpty(),
            'rata' => $nilaiAda->isNotEmpty() ? round($nilaiAda->avg('nilai'), 1) : null,
            'tertinggi' => $nilaiAda->isNotEmpty() ? $nilaiAda->max('nilai') : null,
            'terendah' => $nilaiAda->isNotEmpty() ? $nilaiAda->min('nilai') : null,
            'totalPoin' => (float) $quiz->soal->sum('poin'),
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authQuiz($quiz);

        $valid = $request->validate($this->aturan());
        $quiz->update($this->dataQuiz($request, $valid));

        return back()->with('status', 'Pengaturan quiz diperbarui.');
    }

    /** Ganti seluruh soal dengan file baru — hanya bila belum ada yang mengerjakan. */
    public function gantiSoal(Request $request, Quiz $quiz)
    {
        $this->authQuiz($quiz);

        if ($quiz->percobaan()->exists()) {
            return back()->withErrors(['file' => 'Soal tidak bisa diganti karena sudah ada siswa yang mengerjakan. Buat quiz baru, atau reset percobaan siswa dulu.']);
        }

        $request->validate(['file' => ['required', 'file', 'max:5120', 'extensions:xlsx,xls,docx']], [
            'file.required' => 'Unggah file soal.',
            'file.extensions' => 'File harus berformat .xlsx, .xls, atau .docx.',
        ]);

        $file = $request->file('file');
        [$soal, $error] = QuizTemplateService::baca($file->getRealPath(), $file->getClientOriginalExtension());
        if ($error) {
            return back()->with('import_errors', $error);
        }

        DB::transaction(function () use ($quiz, $soal) {
            $quiz->soal()->delete();
            foreach ($soal as $i => $s) {
                $quiz->soal()->create($s + ['urutan' => $i + 1]);
            }
        });

        return back()->with('status', count($soal) . ' soal baru menggantikan soal sebelumnya.');
    }

    public function status(Request $request, Quiz $quiz)
    {
        $this->authQuiz($quiz);
        $aksi = $request->validate(['aksi' => ['required', 'in:terbit,tutup,draft']])['aksi'];

        if ($aksi === 'terbit') {
            if ($quiz->soal()->count() === 0) {
                return back()->withErrors(['aksi' => 'Quiz belum punya soal.']);
            }
            $quiz->update(['status' => 'terbit']);

            return back()->with('status', 'Quiz diterbitkan. Siswa kini dapat mengerjakan sesuai jadwal.');
        }

        if ($aksi === 'tutup') {
            $quiz->update(['status' => 'ditutup']);

            return back()->with('status', 'Quiz ditutup. Siswa tidak bisa memulai lagi.');
        }

        if ($quiz->percobaan()->exists()) {
            return back()->withErrors(['aksi' => 'Tidak bisa dikembalikan ke draft karena sudah ada yang mengerjakan.']);
        }
        $quiz->update(['status' => 'draft']);

        return back()->with('status', 'Quiz dikembalikan ke draft.');
    }

    /** Hapus semua percobaan seorang siswa agar bisa mengulang. */
    public function reset(Quiz $quiz, Siswa $siswa)
    {
        $this->authQuiz($quiz);
        PercobaanQuiz::where('quiz_id', $quiz->id)->where('siswa_id', $siswa->id)->delete();

        return back()->with('status', "Percobaan {$siswa->nama} direset; ia bisa mengerjakan ulang.");
    }

    public function destroy(Quiz $quiz)
    {
        $pm = $this->authQuiz($quiz);
        $judul = $quiz->judul;
        $quiz->delete();

        return redirect()->route('lms.guru.quiz.index', $pm)->with('status', "Quiz \"{$judul}\" dihapus beserta seluruh hasilnya.");
    }
}
