{{-- resources/views/lms/guru/kelas.blade.php --}}
{{--
    Variabel dari controller:
    $pengampuMapel : collection PengampuMapel (with mataPelajaran, kelas.siswa)
    $tahunAjaran, $semester (opsional)
--}}
@extends('lms.layouts.app')

@section('title', 'Kelas Saya')
@section('breadcrumb', 'Kelas Saya')

@section('content')
    @php
        // Class Tailwind harus ditulis utuh (tidak boleh "text-{{ $c }}-600"),
        // kalau tidak, class-nya tidak ikut ter-build dan warnanya hilang.
        $palet = [
            ['badge' => 'text-blue-600 bg-blue-50',       'icon' => 'bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white'],
            ['badge' => 'text-indigo-600 bg-indigo-50',   'icon' => 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white'],
            ['badge' => 'text-emerald-600 bg-emerald-50', 'icon' => 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white'],
            ['badge' => 'text-amber-600 bg-amber-50',     'icon' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white'],
            ['badge' => 'text-rose-600 bg-rose-50',       'icon' => 'bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white'],
        ];

        // Link fitur lain: otomatis '#' kalau route-nya belum dibuat
        $link = fn($name, $pm) => Route::has($name) ? route($name, $pm) : '#';

        $btnMenu = 'px-2 py-1.5 text-xs font-semibold rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200/70 transition-all flex items-center justify-center gap-1';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 flex-wrap">
                <span>Kelas Saya</span>
                @isset($tahunAjaran)
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">
                        T.A {{ $tahunAjaran }}@isset($semester) — {{ $semester }}@endisset
                    </span>
                @endisset
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Akses cepat ke materi, tugas, penilaian, dan presensi per rombel.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mt-5">
        @forelse ($pengampuMapel as $i => $pm)
            @php $c = $palet[$i % count($palet)]; @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $c['badge'] }} px-2 py-0.5 rounded-md">
                            {{ $pm->kelas->nama_kelas ?? '-' }}
                        </span>
                        <h3 class="font-bold text-slate-900 text-base mt-2 group-hover:text-blue-600 transition-colors">
                            {{ $pm->mataPelajaran->nama ?? '-' }}
                        </h3>
                        <p class="text-xs text-slate-500 flex items-center gap-2 mt-1">
                            <span class="font-medium text-slate-700 flex items-center gap-1">
                                <i class="bi bi-mortarboard text-slate-400"></i> {{ $pm->kelas->nama_kelas ?? '-' }}
                            </span>
                            <span>•</span>
                            <span>{{ $pm->kelas?->siswa?->count() ?? 0 }} Siswa</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl {{ $c['icon'] }} flex items-center justify-center text-lg flex-shrink-0 transition-colors">
                        <i class="bi bi-book-half"></i>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-1.5">
                    <a href="{{ $link('lms.guru.materi.index', $pm) }}" class="{{ $btnMenu }}">
                        <i class="bi bi-journal-text text-slate-400"></i> Materi
                    </a>
                    <a href="{{ $link('lms.guru.tugas.index', $pm) }}" class="{{ $btnMenu }}">
                        <i class="bi bi-clipboard-check text-slate-400"></i> Tugas
                    </a>
                    <a href="{{ $link('lms.guru.nilai.index', $pm) }}" class="{{ $btnMenu }}">
                        <i class="bi bi-clipboard-data text-slate-400"></i> Nilai
                    </a>
                    <a href="{{ $link('lms.guru.modul-ajar.index', $pm) }}"
                        class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-all flex items-center justify-center gap-1">
                        <i class="bi bi-archive text-slate-500"></i> Modul
                    </a>
                    <a href="{{ $link('lms.guru.presensi.index', $pm) }}"
                        class="col-span-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/30 transition-all flex items-center justify-center gap-1.5">
                        <i class="bi bi-qr-code-scan"></i> Buka Presensi
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400 text-sm">
                Belum ada kelas yang Anda ampu.
            </div>
        @endforelse
    </div>
@endsection