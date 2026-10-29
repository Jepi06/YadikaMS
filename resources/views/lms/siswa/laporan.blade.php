@extends('lms.layouts.app')

@section('title', 'Laporan Kehadiran & Pelanggaran - LMS Yadika')
@section('breadcrumb', 'Laporan Saya')

@section('content')
    @php $tg = $poin['tingkat']; $kt = $kehadiran['total']; @endphp

    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan Kehadiran &amp; Poin Kedisiplinan</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $siswa->nama }} • {{ $siswa->kelas->nama_kelas ?? '-' }} • NIS {{ $siswa->nis }}</p>
        </div>
        <a href="{{ route('lms.siswa.laporan.pdf', ['tahun_ajaran' => $tahunAjaran, 'semester' => $semester]) }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-sm">
            <i class="bi bi-download"></i><span>Unduh PDF</span>
        </a>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <select name="tahun_ajaran" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                @foreach ($periodeList->pluck('tahun_ajaran')->push($tahunAjaran)->unique() as $th)
                    <option value="{{ $th }}" @selected($th == $tahunAjaran)>Tahun Ajaran {{ $th }}</option>
                @endforeach
            </select>
            <select name="semester" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                @foreach ($periodeList->pluck('semester')->push($semester)->unique() as $sm)
                    <option value="{{ $sm }}" @selected($sm == $semester)>Semester {{ $sm }}</option>
                @endforeach
            </select>
        </form>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Persentase Kehadiran</p>
            <p class="text-3xl font-bold {{ ($kt['persen'] ?? 100) < 75 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $kt['persen'] !== null ? $kt['persen'] . '%' : '–' }}</p>
            <p class="text-xs text-slate-500">{{ $kt['hadir'] }} hadir dari {{ $kt['pertemuan'] }} pertemuan</p>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Izin / Sakit / Alpa</p>
            <p class="text-3xl font-bold text-slate-900">{{ $kt['izin'] }} / {{ $kt['sakit'] }} / {{ $kt['alpa'] }}</p>
            <p class="text-xs text-slate-500">Semester {{ $semester }} {{ $tahunAjaran }}</p>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Saldo Poin</p>
            <p class="text-3xl font-bold {{ $tg['teks'] }}">{{ $poin['saldo'] }} <span class="text-sm font-normal text-slate-500">poin (awal {{ $poin['deposit'] }})</span></p>
            <p class="text-xs {{ $tg['teks'] }} font-semibold">{{ $tg['label'] }} • −{{ $poin['pelanggaran'] }} / +{{ $poin['prestasi'] }}</p>
        </div>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 font-bold text-slate-900">Kehadiran per Mata Pelajaran</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[560px]">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr><th class="py-3 px-5">Mapel</th><th class="px-3 text-center">Pertemuan</th><th class="px-3 text-center">Hadir</th><th class="px-3 text-center">Izin</th><th class="px-3 text-center">Sakit</th><th class="px-3 text-center">Alpa</th><th class="px-3 text-center">%</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kehadiran['mapel'] as $m)
                        <tr>
                            <td class="py-3 px-5 font-semibold text-slate-800">{{ $m['mapel'] }}</td>
                            <td class="text-center">{{ $m['pertemuan'] }}</td>
                            <td class="text-center">{{ $m['hadir'] }}</td>
                            <td class="text-center">{{ $m['izin'] }}</td>
                            <td class="text-center">{{ $m['sakit'] }}</td>
                            <td class="text-center {{ $m['alpa'] ? 'text-rose-600 font-semibold' : '' }}">{{ $m['alpa'] }}</td>
                            <td class="text-center font-mono font-bold {{ $m['persen'] < 75 ? 'text-rose-600' : 'text-slate-800' }}">{{ $m['persen'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Belum ada data presensi pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 font-bold text-slate-900">Riwayat Poin (Pelanggaran &amp; Prestasi)</div>
        <div class="divide-y divide-slate-100">
            @forelse ($riwayat as $r)
                <div class="flex items-start justify-between gap-3 px-5 py-3 text-sm">
                    <div>
                        <div class="text-slate-800">{{ $r->nama_pelanggaran }}</div>
                        <div class="text-xs text-slate-400">{{ $r->tanggal->format('d M Y') }} • {{ $r->kategori }}@if($r->catatan) • {{ $r->catatan }}@endif</div>
                    </div>
                    <span class="font-mono font-bold {{ $r->poin == 0 ? 'text-slate-400' : ($r->tipe === 'prestasi' ? 'text-emerald-600' : 'text-rose-600') }}">{{ $r->poin == 0 ? '–' : ($r->tipe === 'prestasi' ? '+' : '−') . $r->poin }}</span>
                </div>
            @empty
                <div class="py-8 text-center text-slate-500">Belum ada catatan poin.</div>
            @endforelse
        </div>
    </section>
@endsection
