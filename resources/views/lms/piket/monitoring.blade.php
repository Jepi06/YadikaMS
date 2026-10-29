@extends('lms.layouts.app')

@section('title', 'Monitoring Piket - LMS Yadika')
@section('breadcrumb', 'Monitoring Piket')

@section('content')
    @php
        $badge = [
            'dibuka'     => ['Presensi dibuka', 'bg-emerald-50 text-emerald-700'],
            'belum_buka' => ['Jam berjalan, belum dibuka', 'bg-rose-50 text-rose-700'],
            'terlewat'   => ['Terlewat', 'bg-orange-50 text-orange-700'],
            'menunggu'   => ['Belum mulai', 'bg-slate-100 text-slate-600'],
        ];
    @endphp

    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Monitoring Presensi — Guru Piket</h1>
            <p class="text-sm text-slate-500 mt-1">{{ now()->locale('id')->translatedFormat('l, d F Y') }} • diperbarui {{ now()->format('H:i') }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <form method="GET" class="flex">
                <select name="kelas_id" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                    <option value="">Semua kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </form>
          
        </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['Guru harus buka presensi', $jumlahHarusBuka, 'text-rose-600'],
            ['Presensi terlewat', $jumlahTerlewat, 'text-orange-600'],
            ['Sedang dibuka', $jumlahBuka, 'text-emerald-600'],
            ['Tidak ada jadwal / tutup', $jumlahTutup, 'text-slate-700'],
        ] as [$label, $n, $warna])
            <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">{{ $label }}</p>
                <p class="text-3xl font-bold {{ $warna }}">{{ $n }}</p>
            </div>
        @endforeach
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 font-bold text-slate-900">Jadwal Hari Ini</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[820px]">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr><th class="py-3 px-5">Jam</th><th class="px-3">Kelas</th><th class="px-3">Mapel</th><th class="px-3">Guru</th><th class="px-3">Ruang</th><th class="px-3">Status</th><th class="px-3 text-center">Presensi siswa</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jadwalHariIni as $j)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-5 font-mono whitespace-nowrap">{{ $j['mulai'] }}–{{ $j['selesai'] }}</td>
                            <td class="px-3 font-semibold">{{ $j['kelas'] }}</td>
                            <td class="px-3">{{ $j['mapel'] }}</td>
                            <td class="px-3">{{ $j['guru'] }}</td>
                            <td class="px-3">{{ $j['ruangan'] ?? '-' }}</td>
                            <td class="px-3"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge[$j['status']][1] }}">{{ $badge[$j['status']][0] }}</span></td>
                            <td class="px-3 text-center font-mono">{{ $j['sudah'] !== null ? $j['sudah'] . ' / ' . $j['total'] : '–' }}</td>
                            <td class="px-3 text-right"><a class="text-blue-600 text-xs font-semibold" href="{{ route('lms.guru.piket.monitoring.detail', $j['pengampu_id']) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-slate-500">Tidak ada jadwal hari ini{{ $kelasId ? ' untuk kelas ini' : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 font-bold text-slate-900">Status per Guru</div>
        <div class="divide-y divide-slate-100">
            @forelse ($daftarGuru as $g)
                @php
                    $lbl = ['harus_buka' => ['Harus buka presensi', 'bg-rose-50 text-rose-700'], 'terlewat' => ['Terlewat', 'bg-orange-50 text-orange-700'],
                            'buka' => ['Presensi dibuka', 'bg-emerald-50 text-emerald-700'], 'tutup' => ['Tidak ada jadwal', 'bg-slate-100 text-slate-600']][$g['status']];
                @endphp
                <details class="group">
                    <summary class="flex items-center justify-between gap-3 px-5 py-3 cursor-pointer hover:bg-slate-50">
                        <span class="font-semibold text-slate-900">{{ $g['guru']->name }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $lbl[1] }}">{{ $lbl[0] }}</span>
                    </summary>
                    <div class="px-5 pb-3 text-sm text-slate-600 space-y-1">
                        @foreach ($g['kelas'] as $k)
                            @if ($k['jadwal_status'] || $k['aktif'])
                                <div class="flex justify-between">
                                    <a class="hover:text-blue-600" href="{{ route('lms.guru.piket.monitoring.detail', $k['pengampu_id']) }}">{{ $k['kelas'] }} — {{ $k['mapel'] }} @if($k['jadwal_jam'])<span class="text-slate-400">({{ $k['jadwal_jam'] }})</span>@endif</a>
                                    <span class="font-mono">{{ $k['aktif'] ? $k['sudah'] . '/' . $k['total'] : '–' }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </details>
            @empty
                <div class="py-10 text-center text-slate-500">Tidak ada data guru.</div>
            @endforelse
        </div>
    </section>
@endsection
