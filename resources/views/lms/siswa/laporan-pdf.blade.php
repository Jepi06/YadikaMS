@php
    $tg = $poin['tingkat']; $kt = $kehadiran['total'];
    $tgl = fn($d) => \Illuminate\Support\Carbon::parse($d)->locale('id')->translatedFormat('d F Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Kehadiran &amp; Poin - {{ $siswa->nama }}</title>
<style>
    @page { margin: 28px 36px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1e293b; }
    h1 { font-size: 15px; margin: 0; text-align: center; }
    .sub { text-align: center; color: #64748b; margin: 2px 0 14px; }
    .kop { border-bottom: 2px solid #1e293b; padding-bottom: 8px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    .ident td { padding: 2px 4px; vertical-align: top; }
    .grid th, .grid td { border: 1px solid #cbd5e1; padding: 5px 6px; }
    .grid th { background: #f1f5f9; font-size: 9.5px; text-transform: uppercase; }
    .c { text-align: center; } .red { color: #be123c; font-weight: bold; }
    h2 { font-size: 11.5px; margin: 16px 0 6px; padding-left: 6px; border-left: 4px solid #2563eb; }
    .box { border: 1px solid #cbd5e1; padding: 8px 10px; margin-bottom: 6px; }
    .sum td { width: 33%; border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
    .big { font-size: 20px; font-weight: bold; }
    .ttd td { width: 50%; text-align: center; padding-top: 6px; }
    .muted { color: #64748b; font-size: 9px; }
</style>
</head>
<body>
<div class="kop">
    <h1>LAPORAN KEHADIRAN &amp; POIN KEDISIPLINAN SISWA</h1>
    <div class="sub">SMK Yadika Soreang</div>
</div>

<table class="ident">
    <tr><td width="18%">Nama</td><td width="2%">:</td><td width="40%"><b>{{ $siswa->nama }}</b></td>
        <td width="18%">Tahun Ajaran</td><td width="2%">:</td><td>{{ $tahunAjaran }}</td></tr>
    <tr><td>NIS</td><td>:</td><td>{{ $siswa->nis }}</td>
        <td>Semester</td><td>:</td><td>{{ $semester }}</td></tr>
    <tr><td>Kelas</td><td>:</td><td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
        <td>Wali Kelas</td><td>:</td><td>{{ $wali ?? '-' }}</td></tr>
</table>

<h2>Ringkasan</h2>
<table class="sum">
    <tr>
        <td><div class="muted">Persentase Kehadiran</div><div class="big">{{ $kt['persen'] !== null ? $kt['persen'] . '%' : '-' }}</div>
            <div class="muted">{{ $kt['hadir'] }} dari {{ $kt['pertemuan'] }} pertemuan</div></td>
        <td><div class="muted">Izin / Sakit / Alpa</div><div class="big">{{ $kt['izin'] }} / {{ $kt['sakit'] }} / {{ $kt['alpa'] }}</div></td>
        <td><div class="muted">Saldo Poin (deposit {{ $poin['deposit'] }})</div><div class="big {{ $poin['terpakai'] > 0 ? 'red' : '' }}">{{ $poin['saldo'] }}</div>
            <div class="muted">{{ $tg['label'] }}</div></td>
    </tr>
</table>

<h2>Kehadiran per Mata Pelajaran</h2>
<table class="grid">
    <thead><tr><th style="text-align:left">Mata Pelajaran</th><th>Pertemuan</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>%</th></tr></thead>
    <tbody>
    @forelse ($kehadiran['mapel'] as $m)
        <tr>
            <td>{{ $m['mapel'] }}</td><td class="c">{{ $m['pertemuan'] }}</td><td class="c">{{ $m['hadir'] }}</td>
            <td class="c">{{ $m['izin'] }}</td><td class="c">{{ $m['sakit'] }}</td>
            <td class="c {{ $m['alpa'] ? 'red' : '' }}">{{ $m['alpa'] }}</td>
            <td class="c {{ $m['persen'] < 75 ? 'red' : '' }}">{{ $m['persen'] }}%</td>
        </tr>
    @empty
        <tr><td colspan="7" class="c">Belum ada data presensi.</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Poin Kedisiplinan (deposit {{ $poin['deposit'] }} poin)</h2>
<div class="box"><b>Status: {{ $tg['label'] }}</b><br>{{ $tg['tindakan'] }}</div>
<table class="grid">
    <thead><tr><th width="5%">No</th><th width="15%">Tanggal</th><th style="text-align:left">Pelanggaran / Prestasi</th><th width="9%">Poin</th></tr></thead>
    <tbody>
    @forelse ($riwayat as $i => $r)
        <tr>
            <td class="c">{{ $i + 1 }}</td>
            <td class="c">{{ $tgl($r->tanggal) }}</td>
            <td>{{ $r->nama_pelanggaran }}@if($r->catatan)<br><span class="muted">{{ $r->catatan }}</span>@endif</td>
            <td class="c" style="color: {{ $r->tipe === 'prestasi' ? '#047857' : '#be123c' }}">{{ $r->poin == 0 ? '-' : ($r->tipe === 'prestasi' ? '+' : '-') . $r->poin }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="c">Belum ada catatan poin.</td></tr>
    @endforelse
    </tbody>
    <tfoot>
        <tr><td colspan="3" style="text-align:right">Deposit awal</td><td class="c">{{ $poin['deposit'] }}</td></tr>
        <tr><td colspan="3" style="text-align:right">Pelanggaran</td><td class="c">-{{ $poin['pelanggaran'] }}</td></tr>
        <tr><td colspan="3" style="text-align:right">Prestasi</td><td class="c">+{{ $poin['prestasi'] }}</td></tr>
        <tr><td colspan="3" style="text-align:right"><b>Saldo poin</b></td><td class="c"><b>{{ $poin['saldo'] }}</b></td></tr>
    </tfoot>
</table>

<br>
<table class="ttd">
    <tr><td>Mengetahui,<br>Wali Kelas</td><td>Soreang, {{ $tgl($dicetak) }}<br>Siswa</td></tr>
    <tr><td><br><br><br><br>( {{ $wali ?? '................................' }} )</td><td><br><br><br><br>( {{ $siswa->nama }} )</td></tr>
</table>

<p class="muted" style="margin-top:14px">Dokumen dibuat otomatis oleh LMS Yadika pada {{ $dicetak->format('d/m/Y H:i') }}. Persentase kehadiran = jumlah hadir ÷ jumlah pertemuan; pertemuan tanpa catatan dihitung alpa.</p>
</body>
</html>
