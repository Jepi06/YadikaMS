<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NilaiController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    /** Rekap nilai lengkap: tugas, praktik, STS, SAS, sikap -> nilai akhir. */
    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $rekap = NilaiAkhirService::hitung($pengampuMapel);

        return view('lms.guru.nilai', [
            'pengampuMapel' => $pengampuMapel,
            'bobot' => $rekap['bobot'],
            'bw' => $rekap['bobot_efektif'],
            'adaPraktik' => $rekap['ada_praktik'],
            'adaQuiz' => $rekap['ada_quiz'],
            'rekapSiswa' => $rekap['siswa'],
        ]);
    }

    /**
     * Simpan bobot & opsi penilaian.
     * Komponen yang sedang tidak aktif (mis. praktik saat mode "gabung") tidak ikut dihitung
     * ke total 100% dan nilainya dipertahankan, jadi aman bolak-balik ganti mode.
     */
    public function simpanBobot(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'praktik_mode' => ['required', 'in:gabung,terpisah'],
            'sikap_mode' => ['required', 'in:include,tambahan'],
            'bobot_tugas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_praktik' => ['nullable', 'integer', 'min:0', 'max:100'],
            'bobot_sts' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sikap' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sikap_bonus_persen' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sikap_bonus_maks' => ['nullable', 'integer', 'min:0', 'max:30'],
            'quiz_mode' => ['required', 'in:nonaktif,include,tambahan'],
            'bobot_quiz' => ['nullable', 'integer', 'min:0', 'max:100'],
            'quiz_bonus_persen' => ['nullable', 'integer', 'min:0', 'max:100'],
            'quiz_bonus_maks' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        $bobot = BobotNilai::firstOrNew(['pengampu_mapel_id' => $pengampuMapel->id]);

        $terpisah = $data['praktik_mode'] === 'terpisah';
        $include = $data['sikap_mode'] === 'include';

        $bobotPraktik = $terpisah ? (int) ($data['bobot_praktik'] ?? 0) : (int) ($bobot->bobot_praktik ?? 0);
        $bobotSikap = $include ? (int) ($data['bobot_sikap'] ?? 0) : (int) ($bobot->bobot_sikap ?? 20);

        $quizInclude = $data['quiz_mode'] === 'include';
        $bobotQuiz = $quizInclude ? (int) ($data['bobot_quiz'] ?? 0) : (int) ($bobot->bobot_quiz ?? 0);

        $total = $data['bobot_tugas'] + $data['bobot_sts'] + $data['bobot_sas']
            + ($terpisah ? $bobotPraktik : 0)
            + ($include ? $bobotSikap : 0)
            + ($quizInclude ? $bobotQuiz : 0);

        if ($total !== 100) {
            return back()
                ->withErrors(['bobot_tugas' => "Total bobot komponen yang aktif harus tepat 100% (sekarang {$total}%)."])
                ->withInput();
        }

        // forceFill: tidak bergantung pada $fillable model BobotNilai
        $bobot->forceFill([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'praktik_mode' => $data['praktik_mode'],
            'sikap_mode' => $data['sikap_mode'],
            'bobot_tugas' => $data['bobot_tugas'],
            'bobot_praktik' => $bobotPraktik,
            'bobot_sts' => $data['bobot_sts'],
            'bobot_sas' => $data['bobot_sas'],
            'bobot_sikap' => $bobotSikap,
            'sikap_bonus_persen' => $data['sikap_bonus_persen'] ?? ($bobot->sikap_bonus_persen ?? 10),
            'sikap_bonus_maks' => $data['sikap_bonus_maks'] ?? ($bobot->sikap_bonus_maks ?? 5),
            'quiz_mode' => $data['quiz_mode'],
            'bobot_quiz' => $bobotQuiz,
            'quiz_bonus_persen' => $data['quiz_bonus_persen'] ?? ($bobot->quiz_bonus_persen ?? 10),
            'quiz_bonus_maks' => $data['quiz_bonus_maks'] ?? ($bobot->quiz_bonus_maks ?? 5),
        ])->save();

        return back()->with('status', 'Bobot & opsi penilaian tersimpan.');
    }

    /** Simpan nilai sikap + STS + SAS seluruh siswa sekaligus (bulk). */
    public function simpanSikap(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'sikap' => ['required', 'array'],
            'sikap.*.predikat' => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang'],
            'sikap.*.catatan' => ['nullable', 'string', 'max:255'],
            'ujian' => ['nullable', 'array'],
            'ujian.*.sts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ujian.*.sas' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($data['sikap'] as $siswaId => $row) {
            NilaiSikap::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['predikat' => $row['predikat'], 'catatan' => $row['catatan'] ?? null]
            );
        }

        foreach ($data['ujian'] ?? [] as $siswaId => $row) {
            NilaiUjian::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['nilai_sts' => $row['sts'] ?? null, 'nilai_sas' => $row['sas'] ?? null]
            );
        }

        return back()->with('status', 'Nilai sikap, STS, dan SAS tersimpan.');
    }

    /** Export rekap nilai ke Excel — per tugas & praktik + ringkasan nilai akhir. */
    public function exportExcel(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $rekap = NilaiAkhirService::hitung($pengampuMapel);
        $rekapSiswa = $rekap['siswa'];
        $w = $rekap['bobot_efektif'];

        $daftarTugas = DB::table('tugas')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('batas_waktu')
            ->get(['id', 'judul']);

        $nilaiPerTugas = DB::table('pengumpulan_tugas')
            ->whereIn('tugas_id', $daftarTugas->pluck('id'))
            ->whereNotNull('dinilai_at')
            ->get(['siswa_id', 'tugas_id', 'nilai'])
            ->groupBy('siswa_id');

        $daftarPraktik = DB::table('praktik_lms')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('tanggal')->orderBy('id')
            ->get(['id', 'judul']);

        $nilaiPerPraktik = DB::table('nilai_praktik_lms')
            ->whereIn('praktik_id', $daftarPraktik->pluck('id'))
            ->get(['siswa_id', 'praktik_id', 'nilai'])
            ->groupBy('siswa_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        $namaMapel = $pengampuMapel->mataPelajaran->nama ?? '-';
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? '-';

        $teksBobot = "Tugas {$w['tugas']}%";
        $teksBobot .= $w['praktik_terpisah'] ? " | Praktik {$w['praktik']}%" : ' (termasuk praktik)';
        $teksBobot .= " | STS {$w['sts']}% | SAS {$w['sas']}%";
        $teksBobot .= $w['sikap_include']
            ? " | Sikap {$w['sikap']}%"
            : " | Sikap/keaktifan: poin tambahan ({$w['bonus_persen']}% dari nilai keaktifan, maks +{$w['bonus_maks']} poin)";

        if ($w['quiz_include']) {
            $teksBobot .= " | Quiz {$w['quiz']}%";
        } elseif ($w['quiz_tambahan']) {
            $teksBobot .= " | Quiz: poin tambahan ({$w['quiz_bonus_persen']}% dari rata-rata quiz, maks +{$w['quiz_bonus_maks']} poin)";
        }

        $sheet->setCellValue('A1', 'Rekap Nilai — ' . $namaMapel);
        $sheet->setCellValue('A2', 'Kelas: ' . $namaKelas . ' | Tahun Ajaran: ' . $pengampuMapel->tahun_ajaran . ' ' . $pengampuMapel->semester);
        $sheet->setCellValue('A3', 'Bobot: ' . $teksBobot);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        $baseRow = 5;

        $header = ['No', 'Nama Siswa'];
        foreach ($daftarTugas as $tugas) {
            $header[] = $tugas->judul;
        }
        foreach ($daftarPraktik as $praktik) {
            $header[] = 'Praktik: ' . $praktik->judul;
        }
        $header[] = $w['praktik_terpisah'] ? 'Rata Tugas' : 'Rata Tugas+Praktik';
        if ($w['praktik_terpisah']) {
            $header[] = 'Rata Praktik';
        }
        $header = array_merge($header, ['STS', 'SAS']);
        if ($w['quiz_mode'] !== 'nonaktif') {
            $header[] = 'Rata Quiz';
        }
        $header[] = 'Sikap';
        if (! $w['sikap_include']) {
            $header[] = 'Poin Tambahan Sikap';
        }
        if ($w['quiz_tambahan']) {
            $header[] = 'Poin Tambahan Quiz';
        }
        $header = array_merge($header, ['Nilai Akhir', 'Ket.']);

        foreach ($header as $i => $label) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $baseRow, $label);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($header));
        $headerRange = 'A' . $baseRow . ':' . $lastColLetter . $baseRow;

        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('0D47A1');
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $row = $baseRow + 1;
        foreach ($pengampuMapel->kelas->siswa as $i => $siswa) {
            $col = 1;
            $sheet->setCellValueByColumnAndRow($col++, $row, $i + 1);
            $sheet->setCellValueByColumnAndRow($col++, $row, $siswa->nama);

            $nilaiSiswaTugas = $nilaiPerTugas->get($siswa->id, collect())->keyBy('tugas_id');
            foreach ($daftarTugas as $tugas) {
                $n = $nilaiSiswaTugas->get($tugas->id);
                $sheet->setCellValueByColumnAndRow($col++, $row, $n ? (float) $n->nilai : '-');
            }

            $nilaiSiswaPraktik = $nilaiPerPraktik->get($siswa->id, collect())->keyBy('praktik_id');
            foreach ($daftarPraktik as $praktik) {
                $n = $nilaiSiswaPraktik->get($praktik->id);
                $sheet->setCellValueByColumnAndRow($col++, $row, ($n && $n->nilai !== null) ? (float) $n->nilai : '-');
            }

            $r = $rekapSiswa->get($siswa->id);
            $rataKomp = $r->rata_komponen_tugas;
            $sheet->setCellValueByColumnAndRow($col++, $row, $rataKomp !== null ? round($rataKomp, 1) : '-');
            if ($w['praktik_terpisah']) {
                $sheet->setCellValueByColumnAndRow($col++, $row, $r->rata_praktik !== null ? round($r->rata_praktik, 1) : '-');
            }
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_sts ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_sas ?? '-');
            if ($w['quiz_mode'] !== 'nonaktif') {
                $sheet->setCellValueByColumnAndRow($col++, $row, $r->rata_quiz !== null ? round($r->rata_quiz, 1) : '-');
            }
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->sikap_predikat ?? '-');
            if (! $w['sikap_include']) {
                $sheet->setCellValueByColumnAndRow($col++, $row, $r->bonus_sikap);
            }
            if ($w['quiz_tambahan']) {
                $sheet->setCellValueByColumnAndRow($col++, $row, $r->bonus_quiz);
            }
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_akhir);
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->lengkap ? 'Lengkap' : 'Belum Lengkap');

            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A{$baseRow}:{$lastColLetter}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        for ($c = 1; $c <= count($header); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        $namaFile = 'Rekap-Nilai-' . str_replace(' ', '-', $namaMapel) . '-' . str_replace(' ', '-', $namaKelas) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
