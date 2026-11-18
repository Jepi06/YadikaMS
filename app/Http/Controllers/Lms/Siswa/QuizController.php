<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\JawabanQuiz;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PercobaanQuiz;
use App\Models\Lms\Quiz;
use App\Models\Lms\SoalQuiz;
use App\Models\Siswa;
use App\Services\Lms\QuizService;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    private function siswa(): Siswa
    {
        return Siswa::with('kelas')->where('user_id', Auth::guard('lms')->id())->firstOrFail();
    }

    private function milikku(PercobaanQuiz $p): Siswa
    {
        $siswa = $this->siswa();
        abort_unless((int) $p->siswa_id === (int) $siswa->id, 403);

        return $siswa;
    }

    /** Daftar quiz di semua mapel kelas siswa. */
    public function index()
    {
        $siswa = $this->siswa();

        $pmIds = PengampuMapel::where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->where('semester', TahunAjaran::semesterSekarang())
            ->pluck('id');

        $quiz = Quiz::with('pengampuMapel.mataPelajaran')->withCount('soal')
            ->whereIn('pengampu_mapel_id', $pmIds)->where('status', '!=', 'draft')
            ->orderByDesc('id')->get();

        QuizService::finalisasiKadaluarsa($quiz->pluck('id')->all());

        $percobaan = PercobaanQuiz::where('siswa_id', $siswa->id)->whereIn('quiz_id', $quiz->pluck('id'))
            ->orderBy('id')->get()->groupBy('quiz_id');

        $baris = $quiz->map(function ($q) use ($percobaan) {
            $p = $percobaan->get($q->id, collect());
            $berjalan = $p->first(fn($x) => $x->status === 'berjalan' && now()->lt($x->deadline_at));
            $selesai = $p->where('status', 'selesai');

            return (object) [
                'quiz' => $q,
                'berjalan' => $berjalan,
                'terbaik' => $selesai->sortByDesc('nilai')->first(),
                'jumlahSelesai' => $selesai->count(),
                'sisa' => max(0, $q->maks_percobaan - $p->count()),
                'bisaMulai' => $q->bisaDimulai() && $q->soal_count > 0 && ($q->maks_percobaan - $p->count()) > 0,
            ];
        });

        return view('lms.siswa.quiz.index', compact('baris', 'siswa'));
    }

    public function mulai(Quiz $quiz)
    {
        $siswa = $this->siswa();
        $pm = $quiz->pengampuMapel;
        abort_unless($pm && (int) $pm->kelas_id === (int) $siswa->kelas_id, 403);

        QuizService::finalisasiKadaluarsa([$quiz->id]);

        // lanjutkan percobaan yang masih berjalan
        $berjalan = PercobaanQuiz::where('quiz_id', $quiz->id)->where('siswa_id', $siswa->id)
            ->where('status', 'berjalan')->where('deadline_at', '>', now())->first();
        if ($berjalan) {
            return redirect()->route('lms.siswa.quiz.kerjakan', $berjalan);
        }

        if (! $quiz->bisaDimulai()) {
            return back()->withErrors(['quiz' => 'Quiz ini belum dibuka atau sudah ditutup.']);
        }

        $dipakai = PercobaanQuiz::where('quiz_id', $quiz->id)->where('siswa_id', $siswa->id)->count();
        if ($dipakai >= $quiz->maks_percobaan) {
            return back()->withErrors(['quiz' => 'Kesempatan mengerjakan quiz ini sudah habis.']);
        }

        $ids = SoalQuiz::where('quiz_id', $quiz->id)->orderBy('urutan')->pluck('id')->all();
        if (! $ids) {
            return back()->withErrors(['quiz' => 'Quiz belum memiliki soal.']);
        }
        if ($quiz->acak_soal) {
            shuffle($ids);
        }

        $p = PercobaanQuiz::create([
            'quiz_id' => $quiz->id,
            'siswa_id' => $siswa->id,
            'mulai_at' => now(),
            'deadline_at' => now()->addMinutes($quiz->durasi_menit),
            'status' => 'berjalan',
            'urutan_soal' => $ids,
            'total_soal' => count($ids),
        ]);

        return redirect()->route('lms.siswa.quiz.kerjakan', $p);
    }

    public function kerjakan(PercobaanQuiz $percobaan)
    {
        $this->milikku($percobaan);

        if ($percobaan->status === 'selesai') {
            return redirect()->route('lms.siswa.quiz.hasil', $percobaan);
        }
        if (now()->gte($percobaan->deadline_at)) {
            QuizService::selesaikan($percobaan);

            return redirect()->route('lms.siswa.quiz.hasil', $percobaan)->with('status', 'Waktu habis — jawabanmu otomatis dikumpulkan.');
        }

        $quiz = $percobaan->quiz;
        $soal = SoalQuiz::where('quiz_id', $quiz->id)->get()->keyBy('id');
        $urut = collect($percobaan->urutan_soal ?? [])->map(fn($id) => $soal->get($id))->filter()->values();

        // kunci jawaban TIDAK dikirim ke browser
        $tampil = $urut->map(fn($s) => ['id' => $s->id, 'pertanyaan' => $s->pertanyaan, 'opsi' => $s->opsi()]);

        $tersimpan = JawabanQuiz::where('percobaan_id', $percobaan->id)->pluck('jawaban', 'soal_id');

        return view('lms.siswa.quiz.kerjakan', [
            'percobaan' => $percobaan,
            'quiz' => $quiz,
            'soal' => $tampil,
            'tersimpan' => $tersimpan,
            'sisaDetik' => max(0, (int) now()->diffInSeconds($percobaan->deadline_at, false)),
        ]);
    }

    /** Simpan satu jawaban (dipanggil otomatis saat siswa memilih). */
    public function jawab(Request $request, PercobaanQuiz $percobaan)
    {
        $this->milikku($percobaan);

        if ($percobaan->status !== 'berjalan'
            || now()->gt($percobaan->deadline_at->copy()->addSeconds(QuizService::TOLERANSI_DETIK))) {
            return response()->json(['ok' => false, 'pesan' => 'Waktu pengerjaan sudah habis.'], 409);
        }

        $data = $request->validate([
            'soal_id' => ['required', 'integer'],
            'jawaban' => ['nullable', 'in:A,B,C,D,E'],
        ]);

        abort_unless(in_array((int) $data['soal_id'], array_map('intval', $percobaan->urutan_soal ?? []), true), 422);

        JawabanQuiz::updateOrCreate(
            ['percobaan_id' => $percobaan->id, 'soal_id' => $data['soal_id']],
            ['jawaban' => $data['jawaban'] ?? null]
        );

        return response()->json(['ok' => true]);
    }

    public function selesai(PercobaanQuiz $percobaan)
    {
        $this->milikku($percobaan);
        QuizService::selesaikan($percobaan);

        return redirect()->route('lms.siswa.quiz.hasil', $percobaan);
    }

    public function hasil(PercobaanQuiz $percobaan)
    {
        $this->milikku($percobaan);

        if ($percobaan->status !== 'selesai') {
            return redirect()->route('lms.siswa.quiz.kerjakan', $percobaan);
        }

        $quiz = $percobaan->quiz;
        $pembahasan = null;

        if ($quiz->tampil_pembahasan) {
            $jawab = JawabanQuiz::where('percobaan_id', $percobaan->id)->pluck('jawaban', 'soal_id');
            $soal = SoalQuiz::where('quiz_id', $quiz->id)->orderBy('urutan')->get();
            $pembahasan = $soal->map(fn($s) => (object) [
                'soal' => $s,
                'jawaban' => $jawab->get($s->id),
                'benar' => $jawab->get($s->id) === $s->kunci,
            ]);
        }

        return view('lms.siswa.quiz.hasil', compact('percobaan', 'quiz', 'pembahasan'));
    }
}
