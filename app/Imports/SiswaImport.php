<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows,
    WithChunkReading
{
    private int   $importedCount = 0;
    private array $skippedRows   = [];
    private array $errors        = [];

    /**
     * Kolom yang diharapkan di Excel (heading row):
     *   nis | nama | jenis_kelamin | alamat | no_hp
     *   nama_kelas | jurusan | tingkat | tahun_ajaran | nama_wali_kelas
     *
     * Kolom nama_kelas+jurusan+tingkat dipakai untuk MENCARI kelas yang
     * sudah ada. Kalau kelas belum ada, baris akan diskip (bukan dibuat
     * kelas baru) agar tidak ada data orphan.
     */
   public function collection(Collection $rows): void
{
    // Baris 1-4 adalah info kelas, data mulai baris 7 (index 0 = baris 6 heading, index 1+ = data)
    // WithHeadingRow membaca baris 6 sebagai heading, jadi $rows sudah berisi data baris 7+
    // Tapi nama_kelas diambil dari cell B1 yang kita simpan via sheet title / active sheet info

    // Ambil nama kelas dari baris info yang di-inject lewat importWithSheet
    // Gunakan $this->kelasId yang di-set sebelum import per sheet
    
    $kelas = isset($this->kelasId)
        ? \App\Models\Kelas::find($this->kelasId)
        : null;

    foreach ($rows as $rowNum => $row) {
        // Skip baris info (nis kosong) — baris 1-4 info + baris contoh
        $nis  = trim((string) ($row['nis']  ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        if (empty($nis) || empty($nama) || $nis === '1234567890') continue;

        $jkNorm = $this->normalizeJK(strtoupper(trim((string) ($row['jenis_kelamin'] ?? ''))));
        if (!$jkNorm) {
            $this->errors[] = "NIS {$nis}: jenis kelamin tidak dikenali.";
            continue;
        }

        if (\App\Models\Mapping\Siswa::where('nis', $nis)->exists()) {
            $this->skippedRows[] = ['nis' => $nis, 'nama' => $nama, 'alasan' => 'NIS duplikat'];
            continue;
        }

        if (!$kelas) {
            $this->errors[] = "NIS {$nis}: kelas tidak ditemukan.";
            continue;
        }

        \App\Models\Mapping\Siswa::create([
            'nis'           => $nis,
            'nama'          => $nama,
            'jenis_kelamin' => $jkNorm,
            'alamat'        => trim((string) ($row['alamat'] ?? '')) ?: null,
            'no_hp'         => trim((string) ($row['no_hp']  ?? '')) ?: null,
            'kelas_id'      => $kelas->id,
        ]);

        $this->importedCount++;
    }
}

public ?int $kelasId = null;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function normalizeJK(string $raw): ?string
    {
        $map = [
            'L'          => 'L',
            'LAKI'       => 'L',
            'LAKI-LAKI'  => 'L',
            'LAKI LAKI'  => 'L',
            'P'          => 'P',
            'PEREMPUAN'  => 'P',
            'WANITA'     => 'P',
        ];
        return $map[strtoupper($raw)] ?? null;
    }

    // ── Getters ───────────────────────────────────────────────────────────────

    public function getImportedCount(): int  { return $this->importedCount; }
    public function getSkippedRows(): array  { return $this->skippedRows; }
    public function getErrors(): array       { return $this->errors; }

    // ── Chunk reading (hemat memori untuk file besar) ─────────────────────────

    public function chunkSize(): int { return 500; }

    // ── Download template Excel ────────────────────────────────────────────────

  public function downloadTemplate(): StreamedResponse
{
    $spreadsheet = new Spreadsheet();

    // ── Ambil semua kelas dengan wali kelas dan jurusan ──────────────
    $semuaKelas = \App\Models\Kelas::with(['jurusan', 'waliKelas'])
        ->orderBy('tingkat')
        ->orderBy('nama_kelas')
        ->get();

    $sheetIndex = 0;

    foreach ($semuaKelas as $kelas) {
        $sheet = $sheetIndex === 0
            ? $spreadsheet->getActiveSheet()
            : $spreadsheet->createSheet();

        $namaSheet = substr($kelas->nama_kelas, 0, 31); // maks 31 karakter
        $sheet->setTitle($namaSheet);

        // ── Info kelas di baris 1-3 ──────────────────────────────────
        $sheet->setCellValue('A1', 'Kelas');
        $sheet->setCellValue('B1', $kelas->nama_kelas);
        $sheet->setCellValue('A2', 'Jurusan');
        $sheet->setCellValue('B2', $kelas->jurusan->nama ?? '-');
        $sheet->setCellValue('A3', 'Wali Kelas');
        $sheet->setCellValue('B3', $kelas->waliKelas->name ?? 'Belum ada');
        $sheet->setCellValue('A4', 'Tingkat');
        $sheet->setCellValue('B4', $kelas->tingkat);

        // Style info kelas
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:B4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EFF6FF');

        // ── Heading kolom mulai baris 6 ──────────────────────────────
        $headers = ['A6' => 'nis', 'B6' => 'nama', 'C6' => 'jenis_kelamin', 'D6' => 'alamat', 'E6' => 'no_hp'];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1D4ED8');
        }

        // ── Contoh data mulai baris 7 ────────────────────────────────
        $sheet->fromArray(
            ['1234567890', 'Nama Siswa Contoh', 'L', 'Jl. Contoh No. 1', '081234567890'],
            null, 'A7'
        );
        $sheet->fromArray(
            ['0987654321', 'Nama Siswa Contoh 2', 'P', 'Jl. Contoh No. 2', '089876543210'],
            null, 'A8'
        );

        // ── Auto width ───────────────────────────────────────────────
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Freeze baris heading ─────────────────────────────────────
        $sheet->freezePane('A7');

        $sheetIndex++;
    }

    $spreadsheet->setActiveSheetIndex(0);

    return response()->streamDownload(function () use ($spreadsheet) {
        (new Xlsx($spreadsheet))->save('php://output');
    }, 'template_import_siswa_perkelas.xlsx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}
}
