<?php

namespace App\Services\Lms;

use App\Models\Lms\JawabanQuiz;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PercobaanQuiz;
use App\Models\Lms\Quiz;
use App\Models\Lms\SoalQuiz;

class QuizService
{
    /** Toleransi (detik) setelah deadline untuk jawaban yang masih masuk. */
    public const TOLERANSI_DETIK = 15;

    /** Nilai percobaan dari jawaban tersimpan, lalu tutup percobaan. */
    public static function selesaikan(PercobaanQuiz $p): PercobaanQuiz
    {
        if ($p->status === 'selesai') {
            return $p;
        }

        $soal = SoalQuiz::where('quiz_id', $p->quiz_id)->get()->keyBy('id');
        $didapat = 0.0;
        $benar = 0;

        foreach (JawabanQuiz::where('percobaan_id', $p->id)->get() as $j) {
            $s = $soal->get($j->soal_id);
            if (! $s) {
                continue;
            }
            $ok = $j->jawaban !== null && $j->jawaban === $s->kunci;
            if ($j->benar !== $ok) {
                $j->update(['benar' => $ok]);
            }
            if ($ok) {
                $didapat += (float) $s->poin;
                $benar++;
            }
        }

        $total = (float) $soal->sum('poin');

        $p->forceFill([
            'status' => 'selesai',
            'selesai_at' => now()->min($p->deadline_at),
            'poin_didapat' => $didapat,
            'poin_total' => $total,
            'benar' => $benar,
            'total_soal' => $soal->count(),
            'nilai' => $total > 0 ? round($didapat / $total * 100, 2) : 0,
        ])->save();

        return $p;
    }

    /** Tutup otomatis percobaan yang waktunya sudah habis (siswa menutup browser, dll). */
    public static function finalisasiKadaluarsa(array $quizIds): void
    {
        if (! $quizIds) {
            return;
        }

        PercobaanQuiz::whereIn('quiz_id', $quizIds)
            ->where('status', 'berjalan')
            ->where('deadline_at', '<', now()->subSeconds(self::TOLERANSI_DETIK))
            ->get()
            ->each(fn($p) => self::selesaikan($p));
    }

    /**
     * Rata-rata nilai quiz per siswa untuk 1 pengampu mapel.
     * Quiz dihitung: yang sudah dikerjakan siswa (nilai terbaik), ATAU yang sudah berakhir
     * (ditutup / lewat batas waktu) tapi tidak dikerjakan → dihitung 0.
     *
     * @return array<int, array{rata: float|null, jumlah: int}> keyed by siswa_id
     */
    public static function rataPerSiswa(PengampuMapel $pengampuMapel): array
    {
        $quiz = Quiz::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereIn('status', ['terbit', 'ditutup'])->get();

        if ($quiz->isEmpty()) {
            return [];
        }

        self::finalisasiKadaluarsa($quiz->pluck('id')->all());

        $terbaik = PercobaanQuiz::whereIn('quiz_id', $quiz->pluck('id'))
            ->where('status', 'selesai')
            ->groupBy('quiz_id', 'siswa_id')
            ->selectRaw('quiz_id, siswa_id, MAX(nilai) as n')
            ->get()
            ->groupBy('siswa_id')
            ->map(fn($g) => $g->pluck('n', 'quiz_id'));

        $hasil = [];
        foreach ($pengampuMapel->kelas->siswa as $siswa) {
            $milik = $terbaik->get($siswa->id, collect());
            $daftar = [];
            foreach ($quiz as $q) {
                if ($milik->has($q->id)) {
                    $daftar[] = (float) $milik[$q->id];
                } elseif ($q->sudahBerakhir()) {
                    $daftar[] = 0.0;
                }
            }
            $hasil[$siswa->id] = [
                'rata' => $daftar ? array_sum($daftar) / count($daftar) : null,
                'jumlah' => count($daftar),
            ];
        }

        return $hasil;
    }
}
