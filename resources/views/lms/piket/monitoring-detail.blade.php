@extends('lms.layouts.app')

@section('title', 'Detail Presensi - LMS Yadika')
@section('breadcrumb', 'Detail Presensi')

@section('content')
    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <a href="{{ route('lms.guru.piket.monitoring', ['kelas_id' => $pengampuMapel->kelas_id]) }}" class="text-xs text-blue-600 font-semibold"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} — {{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h1>
            <p class="text-sm text-slate-500">Guru: {{ $pengampuMapel->guru->name ?? '-' }} • {{ \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-emerald-700 font-semibold">{{ $sudah }} sudah</span>
            <span class="text-rose-600 font-semibold">{{ $belum }} belum</span>
            <a href="{{ route('lms.guru.pelanggaran.create', ['kelas_id' => $pengampuMapel->kelas_id]) }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold">Catat Pelanggaran</a>
        </div>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm divide-y divide-slate-100">
        @foreach ($rows as $r)
            <div class="flex items-center justify-between px-5 py-3 text-sm">
                <div>
                    <div class="font-semibold text-slate-900">{{ $r['siswa']->nama }}</div>
                    <div class="text-xs text-slate-400 font-mono">{{ $r['siswa']->nis }}</div>
                </div>
                @if ($r['presensi'])
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $r['presensi']->status === 'Hadir' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $r['presensi']->status }}
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">Belum presensi</span>
                @endif
            </div>
        @endforeach
    </section>
@endsection
