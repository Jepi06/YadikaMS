<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiPraktik;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PengaturanNilai;
use App\Models\Lms\Praktik;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
            'pengaturan' => $rekap['pengaturan'],
            'jumlahPraktik' => $rekap['jumlah_praktik'],
            'rekapSiswa' => $rekap['siswa'],
        ]);
    }

    /**
     * Simpan pengaturan & bobot penilaian (komponen aktif harus total 100%).
     *
     * - praktik_mode : gabung  -> praktik ikut persentase Tugas (4 komponen + sikap)
     *                  terpisah -> praktik punya bobot sendiri (5 komponen)
     * - sikap_mode   : utama    -> sikap ikut total 100%
     *                  tambahan -> sikap jadi poin bonus (maks. sikap_maks_tambahan), tidak masuk 100%
     */
    public function simpanBobot(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $praktikTerpisah = $request->input('praktik_mode') === 'terpisah';
        $sikapUtama = $request->input('sikap_mode') !== 'tambahan';

        $data = $request->validate([
            'praktik_mode' => ['required', 'in:gabung,terpisah'],
            'sikap_mode' => ['required', 'in:utama,tambahan'],
            'bobot_tugas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sts' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_praktik' => [Rule::requiredIf($praktikTerpisah), 'nullable', 'integer', 'min:0', 'max:100'],
            'bobot_sikap' => [Rule::requiredIf($sikapUtama), 'nullable', 'integer', 'min:0', 'max:100'],
            'sikap_maks_tambahan' => [Rule::requiredIf(! $sikapUtama), 'nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $total = $data['bobot_tugas'] + $data['bobot_sts'] + $data['bobot_sas']
            + ($praktikTerpisah ? (int) $data['bobot_praktik'] : 0)
            + ($sikapUtama ? (int) $data['bobot_sikap'] : 0);

        if ($total !== 100) {
            return back()
                ->withErrors(['bobot_tugas' => "Total bobot komponen aktif harus tepat 100% (sekarang {$total}%)."])
                ->withInput();
        }

        $bobotData = [
            'bobot_tugas' => $data['bobot_tugas'],
            'bobot_sts' => $data['bobot_sts'],
            'bobot_sas' => $data['bobot_sas'],
        ];
        if ($sikapUtama) {
            $bobotData['bobot_sikap'] = $data['bobot_sikap'];
        }

        BobotNilai::updateOrCreate(['pengampu_mapel_id' => $pengampuMapel->id], $bobotData);

        $pengaturanData = [
            'praktik_gabung_tugas' => ! $praktikTerpisah,
            'sikap_mode' => $data['sikap_mode'],
        ];
        if ($praktikTerpisah) {
            $pengaturanData['bobot_praktik'] = (int) $data['bobot_praktik'];
        }
        if (! $sikapUtama) {
            $pengaturanData['sikap_maks_tambahan'] = (int) $data['sikap_maks_tambahan'];
        }

        PengaturanNilai::updateOrCreate(['pengampu_mapel_id' => $pengampuMapel->id], $pengaturanData);

        return back()->with('status', 'Pengaturan bobot penilaian tersimpan.');
    }

    /** Simpan nilai sikap (predikat atau custom) + STS + SAS seluruh siswa sekaligus (bulk). */
    public function simpanSikap(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'sikap' => ['required', 'array'],
            'sikap.*.predikat' => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang'],
            'sikap.*.catatan' => ['nullable', 'string', 'max:255'],
            'sikap.*.custom' => ['nullable', 'boolean'],
            'sikap.*.nilai_custom' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ujian' => ['nullable', 'array'],
            'ujian.*.sts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ujian.*.sas' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $pengampuMapel->load('kelas.siswa');
        $siswaKelas = $pengampuMapel->kelas->siswa->keyBy('id');

        // Cek dulu: siswa yang dicentang "custom" wajib punya angka.
        $errors = [];
        foreach ($data['sikap'] as $siswaId => $row) {
            if (! $siswaKelas->has($siswaId)) {
                continue;
            }
            $custom = ! empty($row['custom']);
            $angka = $row['nilai_custom'] ?? null;
            if ($custom && ($angka === null || $angka === '')) {
                $errors[] = $siswaKelas[$siswaId]->nama;
            }
        }
        if ($errors) {
            return back()
                ->withErrors(['sikap' => 'Nilai sikap custom belum diisi untuk: ' . implode(', ', $errors) . '.'])
                ->withInput();
        }

        $tabelSikap = (new NilaiSikap)->getTable();

        foreach ($data['sikap'] as $siswaId => $row) {
            if (! $siswaKelas->has($siswaId)) {
                continue;
            }

            NilaiSikap::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['predikat' => $row['predikat'], 'catatan' => $row['catatan'] ?? null]
            );

            $custom = ! empty($row['custom']);
            DB::table($tabelSikap)
                ->where('pengampu_mapel_id', $pengampuMapel->id)
                ->where('siswa_id', $siswaId)
                ->update([
                    'is_custom' => $custom,
                    'nilai_custom' => $custom ? $row['nilai_custom'] : null,
                ]);
        }

        foreach ($data['ujian'] ?? [] as $siswaId => $row) {
            if (! $siswaKelas->has($siswaId)) {
                continue;
            }

            NilaiUjian::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['nilai_sts' => $row['sts'] ?? null, 'nilai_sas' => $row['sas'] ?? null]
            );
        }

        return back()->with('status', 'Nilai sikap, STS, dan SAS tersimpan.');
    }

    /** Export rekap nilai ke Excel — per tugas + per praktik + STS/SAS/Sikap/Nilai Akhir. */
    public function exportExcel(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $rekap = NilaiAkhirService::hitung($pengampuMapel);
        $rekapSiswa = $rekap['siswa'];
        $bobot = $rekap['bobot'];
        $pengaturan = $rekap['pengaturan'];

        $gabung = (bool) $pengaturan->praktik_gabung_tugas;
        $sikapTambahan = $pengaturan->sikap_mode === 'tambahan';

        $daftarTugas = DB::table('tugas')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('batas_waktu')
            ->get(['id', 'judul']);

        $nilaiPerTugas = DB::table('pengumpulan_tugas')
            ->whereIn('tugas_id', $daftarTugas->pluck('id'))
            ->whereNotNull('dinilai_at')
            ->get(['siswa_id', 'tugas_id', 'nilai'])
            ->groupBy('siswa_id');

        $daftarPraktik = Praktik::where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get(['id', 'judul']);

        $nilaiPerPraktik = NilaiPraktik::whereIn('praktik_id', $daftarPraktik->pluck('id'))
            ->whereNotNull('nilai')
            ->get(['siswa_id', 'praktik_id', 'nilai'])
            ->groupBy('siswa_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        $put = function (int $col, int $row, $value) use ($sheet) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $row, $value);
        };

        $namaMapel = $pengampuMapel->mataPelajaran->nama ?? '-';
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? '-';

        $teksBobot = "Bobot: Tugas" . ($gabung ? ' (+Praktik)' : '') . " {$bobot->bobot_tugas}%";
        if (! $gabung) {
            $teksBobot .= " | Praktik {$pengaturan->bobot_praktik}%";
        }
        $teksBobot .= " | STS {$bobot->bobot_sts}% | SAS {$bobot->bobot_sas}%";
        $teksBobot .= $sikapTambahan
            ? " | Sikap: tambahan maks. {$pengaturan->sikap_maks_tambahan} poin"
            : " | Sikap {$bobot->bobot_sikap}%";

        $sheet->setCellValue('A1', 'Rekap Nilai — ' . $namaMapel);
        $sheet->setCellValue('A2', 'Kelas: ' . $namaKelas . ' | Tahun Ajaran: ' . $pengampuMapel->tahun_ajaran . ' ' . $pengampuMapel->semester);
        $sheet->setCellValue('A3', $teksBobot);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        $baseRow = 5;

        $header = ['No', 'Nama Siswa'];
        foreach ($daftarTugas as $tugas) {
            $header[] = $tugas->judul;
        }
        $header[] = 'Rata Tugas';
        foreach ($daftarPraktik as $praktik) {
            $header[] = 'Praktik: ' . $praktik->judul;
        }
        $header[] = 'Rata Praktik';
        if ($gabung) {
            $header[] = 'Rata Tugas+Praktik';
        }
        $header = array_merge($header, ['STS', 'SAS', 'Sikap']);
        if ($sikapTambahan) {
            $header[] = 'Poin Tambahan Sikap';
        }
        $header = array_merge($header, ['Nilai Akhir', 'Ket.']);

        foreach ($header as $i => $label) {
            $put($i + 1, $baseRow, $label);
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
            $put($col++, $row, $i + 1);
            $put($col++, $row, $siswa->nama);

            $r = $rekapSiswa->get($siswa->id);

            $nilaiSiswaTugas = $nilaiPerTugas->get($siswa->id, collect())->keyBy('tugas_id');
            foreach ($daftarTugas as $tugas) {
                $n = $nilaiSiswaTugas->get($tugas->id);
                $put($col++, $row, $n ? (float) $n->nilai : '-');
            }
            $put($col++, $row, $r->rata_tugas !== null ? round($r->rata_tugas, 1) : '-');

            $nilaiSiswaPraktik = $nilaiPerPraktik->get($siswa->id, collect())->keyBy('praktik_id');
            foreach ($daftarPraktik as $praktik) {
                $n = $nilaiSiswaPraktik->get($praktik->id);
                $put($col++, $row, $n ? (float) $n->nilai : '-');
            }
            $put($col++, $row, $r->rata_praktik !== null ? round($r->rata_praktik, 1) : '-');
            if ($gabung) {
                $put($col++, $row, $r->rata_gabungan !== null ? round($r->rata_gabungan, 1) : '-');
            }

            $put($col++, $row, $r->nilai_sts ?? '-');
            $put($col++, $row, $r->nilai_sas ?? '-');
            $put($col++, $row, $r->sikap_custom
                ? 'Custom (' . round($r->sikap_nilai, 1) . ')'
                : ($r->sikap_predikat ?? '-'));
            if ($sikapTambahan) {
                $put($col++, $row, $r->bonus_sikap);
            }
            $put($col++, $row, $r->nilai_akhir);
            $put($col++, $row, $r->lengkap ? 'Lengkap' : 'Belum Lengkap');

            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A{$baseRow}:{$lastColLetter}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // range('A', 'AA') tidak jalan untuk kolom > Z, jadi pakai indeks kolom.
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
