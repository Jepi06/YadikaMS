<?php

namespace App\Services\Lms;

use DOMDocument;
use DOMXPath;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use RuntimeException;
use ZipArchive;

/**
 * Template soal quiz (pilihan ganda) dalam format Excel atau Word, dan pembacanya.
 *
 * Kolom (sama untuk Excel & Word):
 *  No | Pertanyaan | Opsi A | Opsi B | Opsi C | Opsi D | Opsi E (opsional) | Kunci (A-E) | Poin (opsional) | Pembahasan (opsional)
 *
 * Baris yang pertanyaannya diawali "[CONTOH]" diabaikan saat import.
 */
class QuizTemplateService
{
    public const KOLOM = [
        'No', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D',
        'Opsi E (opsional)', 'Kunci (A-E)', 'Poin (opsional)', 'Pembahasan (opsional)',
    ];

    public const MAKS_SOAL = 200;

    private const CONTOH = [
        [1, '[CONTOH] Ibu kota Indonesia adalah ...', 'Jakarta', 'Bandung', 'Surabaya', 'Medan', '', 'A', 1, 'Jakarta adalah ibu kota negara.'],
        [2, '[CONTOH] Hasil dari 2 + 2 adalah ...', '3', '4', '5', '6', '', 'B', 2, ''],
    ];

    private const PETUNJUK = [
        'PETUNJUK PENGISIAN TEMPLATE QUIZ',
        '1. Isi soal mulai baris ke-2 pada sheet "Soal". Baris yang diawali [CONTOH] akan diabaikan — hapus atau timpa.',
        '2. Satu baris = satu soal pilihan ganda. Kolom Pertanyaan, Opsi A, dan Opsi B wajib diisi.',
        '3. Opsi C, D, E boleh dikosongkan (soal benar/salah: isi Opsi A = Benar, Opsi B = Salah). Opsi harus berurutan tanpa celah.',
        '4. Kunci diisi satu huruf (A–E) dan harus menunjuk opsi yang terisi.',
        '5. Poin opsional (default 1). Nilai quiz = poin didapat ÷ total poin × 100.',
        '6. Pembahasan opsional; ditampilkan ke siswa jika guru mengaktifkan "Tampilkan pembahasan".',
        '7. Maksimal ' . self::MAKS_SOAL . ' soal per file.',
    ];

    // ───────────────────────── EXCEL ─────────────────────────

    public static function buatXlsx(): Spreadsheet
    {
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Soal');

        foreach (self::KOLOM as $i => $label) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $label);
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D47A1');
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(32);

        foreach (self::CONTOH as $r => $baris) {
            foreach ($baris as $c => $nilai) {
                $sheet->setCellValueByColumnAndRow($c + 1, $r + 2, $nilai);
            }
        }

        foreach (['A' => 6, 'B' => 48, 'C' => 24, 'D' => 24, 'E' => 24, 'F' => 24, 'G' => 24, 'H' => 12, 'I' => 12, 'J' => 40] as $col => $lebar) {
            $sheet->getColumnDimension($col)->setWidth($lebar);
        }
        $sheet->getStyle('A2:J' . (self::MAKS_SOAL + 1))->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('H2:I' . (self::MAKS_SOAL + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:J3')->getFont()->getColor()->setRGB('94A3B8'); // contoh tampil pudar
        $sheet->freezePane('A2');

        // Dropdown kunci A–E
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setAllowBlank(true)->setShowDropDown(false)->setShowErrorMessage(true)
            ->setErrorTitle('Kunci tidak valid')->setError('Pilih A, B, C, D, atau E.')
            ->setFormula1('"A,B,C,D,E"');
        for ($r = 2; $r <= self::MAKS_SOAL + 1; $r++) {
            $sheet->getCell("H{$r}")->setDataValidation(clone $dv);
        }

        $petunjuk = $ss->createSheet();
        $petunjuk->setTitle('Petunjuk');
        foreach (self::PETUNJUK as $i => $baris) {
            $petunjuk->setCellValueByColumnAndRow(1, $i + 1, $baris);
        }
        $petunjuk->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $petunjuk->getColumnDimension('A')->setWidth(120);

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    // ───────────────────────── WORD (.docx, tanpa library tambahan) ─────────────────────────

    /** Buat file .docx sementara; kembalikan path-nya. */
    public static function buatDocx(): string
    {
        $esc = fn($s) => htmlspecialchars((string) $s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $para = fn($teks, $tebal = false, $ukuran = 22) =>
            '<w:p><w:r><w:rPr>' . ($tebal ? '<w:b/>' : '') . '<w:sz w:val="' . $ukuran . '"/></w:rPr>'
            . '<w:t xml:space="preserve">' . $esc($teks) . '</w:t></w:r></w:p>';

        $lebar = [450, 3600, 1700, 1700, 1700, 1700, 1500, 800, 700, 1548];

        $sel = function ($teks, $w, $header = false, $pudar = false) use ($esc) {
            $shd = $header ? '<w:shd w:val="clear" w:color="auto" w:fill="0D47A1"/>' : '';
            $rpr = '<w:rPr>' . ($header ? '<w:b/><w:color w:val="FFFFFF"/>' : '')
                . ($pudar ? '<w:color w:val="94A3B8"/>' : '') . '<w:sz w:val="20"/></w:rPr>';

            return '<w:tc><w:tcPr><w:tcW w:w="' . $w . '" w:type="dxa"/>' . $shd . '</w:tcPr>'
                . '<w:p><w:r>' . $rpr . '<w:t xml:space="preserve">' . $esc($teks) . '</w:t></w:r></w:p></w:tc>';
        };

        $baris = '<w:tr>';
        foreach (self::KOLOM as $i => $label) {
            $baris .= $sel($label, $lebar[$i], true);
        }
        $baris .= '</w:tr>';

        foreach (self::CONTOH as $row) {
            $baris .= '<w:tr>';
            foreach ($row as $i => $nilai) {
                $baris .= $sel($nilai, $lebar[$i], false, true);
            }
            $baris .= '</w:tr>';
        }
        for ($n = 3; $n <= 10; $n++) { // baris kosong untuk diisi
            $baris .= '<w:tr>';
            foreach ($lebar as $i => $w) {
                $baris .= $sel($i === 0 ? $n : '', $w);
            }
            $baris .= '</w:tr>';
        }

        $border = '';
        foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $sisi) {
            $border .= '<w:' . $sisi . ' w:val="single" w:sz="4" w:space="0" w:color="94A3B8"/>';
        }
        $grid = '';
        foreach ($lebar as $w) {
            $grid .= '<w:gridCol w:w="' . $w . '"/>';
        }

        $isi = $para('TEMPLATE SOAL QUIZ (PILIHAN GANDA)', true, 28);
        foreach (array_slice(self::PETUNJUK, 1) as $p) {
            $isi .= $para(str_replace('pada sheet "Soal"', 'pada tabel di bawah', $p), false, 20);
        }
        $isi .= $para('Tambah baris pada tabel bila soal lebih banyak. Jangan ubah/hapus baris judul kolom.', false, 20);
        $isi .= '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/><w:tblBorders>' . $border . '</w:tblBorders></w:tblPr>'
            . '<w:tblGrid>' . $grid . '</w:tblGrid>' . $baris . '</w:tbl>';

        $dokumen = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            . $isi
            . '<w:p/><w:sectPr><w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>'
            . '<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="720" w:footer="720" w:gutter="0"/></w:sectPr>'
            . '</w:body></w:document>';

        $tipe = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>';

        $path = tempnam(sys_get_temp_dir(), 'quiz') . '.docx';
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file template Word.');
        }
        $zip->addFromString('[Content_Types].xml', $tipe);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('word/document.xml', $dokumen);
        $zip->close();

        return $path;
    }

    // ───────────────────────── PEMBACA ─────────────────────────

    /**
     * @return array{0: array<int,array>, 1: string[]}  [daftar soal valid, daftar pesan error]
     */
    public static function baca(string $path, string $ext): array
    {
        $ext = strtolower($ext);

        try {
            $baris = $ext === 'docx' ? self::barisDariDocx($path) : self::barisDariExcel($path);
        } catch (\Throwable $e) {
            return [[], ['File tidak bisa dibaca. Pastikan memakai template yang diunduh dari sini. (' . $e->getMessage() . ')']];
        }

        return self::validasi($baris);
    }

    /** @return array<int,array{0:int,1:array}> [nomorBaris, kolom[]] */
    private static function barisDariExcel(string $path): array
    {
        $ss = IOFactory::load($path);
        $sheet = $ss->getSheetByName('Soal') ?? $ss->getSheet(0);
        $data = $sheet->toArray(null, true, true, false);

        $hasil = [];
        foreach ($data as $i => $row) {
            if ($i === 0) {
                continue; // header
            }
            $hasil[] = [$i + 1, array_map(fn($v) => is_scalar($v) ? trim((string) $v) : '', $row)];
        }

        return $hasil;
    }

    private static function barisDariDocx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('bukan file .docx yang valid');
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            throw new RuntimeException('isi dokumen tidak ditemukan');
        }

        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $tabel = $xp->query('//w:tbl')->item(0);
        if (! $tabel) {
            throw new RuntimeException('tabel soal tidak ditemukan di dokumen');
        }

        $hasil = [];
        foreach ($xp->query('./w:tr', $tabel) as $i => $tr) {
            if ($i === 0) {
                continue; // header
            }
            $kolom = [];
            foreach ($xp->query('./w:tc', $tr) as $tc) {
                $paragraf = [];
                foreach ($xp->query('./w:p', $tc) as $p) {
                    $teks = '';
                    foreach ($xp->query('.//w:t', $p) as $t) {
                        $teks .= $t->nodeValue;
                    }
                    $paragraf[] = $teks;
                }
                $kolom[] = trim(implode("\n", $paragraf));
            }
            $hasil[] = [$i + 1, $kolom];
        }

        return $hasil;
    }

    private static function validasi(array $baris): array
    {
        $soal = [];
        $error = [];

        foreach ($baris as [$no, $r]) {
            $pert = $r[1] ?? '';
            $opsi = ['A' => $r[2] ?? '', 'B' => $r[3] ?? '', 'C' => $r[4] ?? '', 'D' => $r[5] ?? '', 'E' => $r[6] ?? ''];

            if ($pert === '' && implode('', $opsi) === '') {
                continue; // baris kosong
            }
            if (str_starts_with(mb_strtoupper($pert), '[CONTOH]')) {
                continue;
            }

            $e = [];
            if ($pert === '') {
                $e[] = 'pertanyaan kosong';
            }
            if ($opsi['A'] === '' || $opsi['B'] === '') {
                $e[] = 'Opsi A dan Opsi B wajib diisi';
            }
            $prev = true;
            foreach (['C', 'D', 'E'] as $h) {
                if ($opsi[$h] !== '' && ! $prev) {
                    $e[] = "Opsi {$h} terisi padahal opsi sebelumnya kosong";
                }
                $prev = $opsi[$h] !== '';
            }

            $kunci = strtoupper(substr(trim($r[7] ?? ''), 0, 1));
            if (! in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
                $e[] = 'kunci harus A–E';
            } elseif ($opsi[$kunci] === '') {
                $e[] = "kunci {$kunci} menunjuk opsi yang kosong";
            }

            $poinMentah = trim((string) ($r[8] ?? ''));
            $poin = 1.0;
            if ($poinMentah !== '') {
                $poinMentah = str_replace(',', '.', $poinMentah);
                if (! is_numeric($poinMentah) || (float) $poinMentah <= 0 || (float) $poinMentah > 100) {
                    $e[] = 'poin harus angka 0–100';
                } else {
                    $poin = (float) $poinMentah;
                }
            }

            if ($e) {
                $error[] = "Baris {$no}: " . implode('; ', $e) . '.';
                continue;
            }

            $soal[] = [
                'pertanyaan' => $pert,
                'opsi_a' => $opsi['A'], 'opsi_b' => $opsi['B'],
                'opsi_c' => $opsi['C'] ?: null, 'opsi_d' => $opsi['D'] ?: null, 'opsi_e' => $opsi['E'] ?: null,
                'kunci' => $kunci,
                'poin' => $poin,
                'pembahasan' => ($r[9] ?? '') !== '' ? $r[9] : null,
            ];
        }

        if (count($soal) > self::MAKS_SOAL) {
            $error[] = 'Jumlah soal melebihi batas ' . self::MAKS_SOAL . '.';
        }
        if (! $error && ! $soal) {
            $error[] = 'Tidak ada soal yang terbaca. Isi soal di bawah baris judul (baris [CONTOH] diabaikan).';
        }

        return [$error ? [] : $soal, $error];
    }
}
