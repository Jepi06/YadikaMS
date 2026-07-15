<?php

namespace App\Exports;

use App\Models\SPMB\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Seluruh data peserta didik baru, lengkap dengan biodata dan nominal.
     */
    public function collection()
    {
        return Pendaftar::with('jurusan:id,nama')
            ->orderBy('jurusan_id')
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Alamat',
            'Agama',
            'Nama Orang Tua',
            'Asal Sekolah',
            'No. HP',
            'Jurusan',
            'Nominal Pembayaran',
            'Status',
            'Lunas',
            'Catatan Admin',
            'Tanggal Daftar',
        ];
    }

    public function map($pendaftar): array
    {
        return [
            $pendaftar->no_pendaftaran,
            $pendaftar->nama_lengkap,
            $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $pendaftar->alamat,
            $pendaftar->agama,
            $pendaftar->nama_orang_tua,
            $pendaftar->asal_sekolah,
            $pendaftar->no_hp,
            optional($pendaftar->jurusan)->nama,
            (float) $pendaftar->nominal_pembayaran,
            $pendaftar->status_label,
            $pendaftar->is_lunas ? 'Ya' : 'Belum',
            $pendaftar->catatan_admin,
            optional($pendaftar->created_at)->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
