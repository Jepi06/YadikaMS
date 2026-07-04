<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PPDB {{ date('Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0; padding: 0;
        }

        .header {
            background: #1e3a8a;
            color: white;
            padding: 20px 28px;
            margin-bottom: 20px;
        }

        .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p  { margin: 4px 0 0; font-size: 10px; opacity: .8; }

        .content { padding: 0 28px 28px; }

        h2 {
            font-size: 13px;
            color: #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 4px;
            margin: 20px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #1e40af;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
        }

        td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }

        tr:nth-child(even) td { background: #f8faff; }

        .badge-diterima {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .summary-box {
            border: 1.5px solid #bfdbfe;
            background: #eff6ff;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .summary-box table { margin: 0; }
        .summary-box td { border: none; padding: 4px 8px; }
        .summary-box th { background: transparent; color: #1e40af; padding: 4px 8px; }

        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<div class="header">
    <h1>📋 Laporan Penerimaan Siswa Baru (PPDB)</h1>
    <p>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} · Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }}</p>
</div>

<div class="content">

    {{-- Rekap Per Jurusan --}}
    <h2>Rekap Penerimaan Per Jurusan</h2>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Jurusan</th>
                <th>Kuota</th>
                <th>Total Pendaftar</th>
                <th>Diterima</th>
                <th>Pending</th>
                <th>Ditolak</th>
                <th>Sisa Kuota</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $grandDiterima = 0; @endphp
            @foreach($rekapJurusan as $rekap)
            @php
                $grandTotal    += $rekap->total;
                $grandDiterima += $rekap->diterima;
            @endphp
            <tr>
                <td><b>{{ $rekap->kode }}</b></td>
                <td>{{ $rekap->nama }}</td>
                <td style="text-align:center;">{{ $rekap->kuota }}</td>
                <td style="text-align:center;">{{ $rekap->total }}</td>
                <td style="text-align:center;color:#059669;font-weight:bold;">{{ $rekap->diterima }}</td>
                <td style="text-align:center;color:#d97706;">{{ $rekap->pending }}</td>
                <td style="text-align:center;color:#dc2626;">{{ $rekap->ditolak }}</td>
                <td style="text-align:center;">{{ $rekap->kuota - $rekap->diterima }}</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold;background:#dbeafe;">
                <td colspan="3">TOTAL</td>
                <td style="text-align:center;">{{ $grandTotal }}</td>
                <td style="text-align:center;color:#059669;">{{ $grandDiterima }}</td>
                <td></td><td></td><td></td>
            </tr>
        </tbody>
    </table>

    {{-- Detail Per Jurusan --}}
    @foreach($pendaftarDiterima as $namaJurusan => $daftarSiswa)
    <h2>Siswa Diterima — {{ $namaJurusan }}</h2>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>No. Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Asal Sekolah</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daftarSiswa as $i => $siswa)
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td style="font-family:monospace;font-size:9px;">{{ $siswa->no_pendaftaran }}</td>
                <td><b>{{ $siswa->nama_lengkap }}</b></td>
                <td>{{ $siswa->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                <td>{{ $siswa->asal_sekolah }}</td>
                <td>{{ $siswa->nominal_formatted }}</td>
                <td><span class="badge-diterima">✓ DITERIMA</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        Total siswa diterima di jurusan <b>{{ $namaJurusan }}</b>: <b>{{ $daftarSiswa->count() }} siswa</b>
    </div>
    @endforeach

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem PPDB SMK · {{ now()->isoFormat('D MMMM Y') }}
    </div>
</div>

</body>
</html>
