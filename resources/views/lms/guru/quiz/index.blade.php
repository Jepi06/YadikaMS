{{-- Variabel: $pengampuMapel, $quiz (koleksi Quiz withCount soal), $peserta [quiz_id => n], $jumlahSiswa --}}
@extends('lms.layouts.app')

@section('title', 'Quiz - LMS Yadika')
@section('breadcrumb', 'Quiz')

@section('content')
    @php
        $namaMapel = $pengampuMapel->mataPelajaran->nama ?? 'Mata Pelajaran';
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? '-';
        $badge = [
            'draft' => ['Draft', 'bg-slate-100 text-slate-600'],
            'terbit' => ['Terbit', 'bg-emerald-50 text-emerald-700'],
            'ditutup' => ['Ditutup', 'bg-amber-50 text-amber-700'],
        ];
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600">Kelas {{ $namaKelas }}</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Quiz</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quiz</h1>
                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $namaMapel }}</span>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $namaKelas }} • {{ $pengampuMapel->semester }} {{ $pengampuMapel->tahun_ajaran }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start md:self-auto">
            <a href="{{ route('lms.guru.kelas.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm"><i class="bi bi-arrow-left"></i>Kembali</a>
            <a href="{{ route('lms.guru.nilai.index', $pengampuMapel) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm"><i class="bi bi-clipboard-data"></i>Rekap Nilai</a>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex gap-2"><i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span></div>
    @endif
    @if (session('import_errors'))
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
            <div class="font-semibold mb-1"><i class="bi bi-exclamation-triangle-fill"></i> File belum bisa diimpor — perbaiki dulu:</div>
            <ul class="list-disc ml-5 space-y-0.5 max-h-56 overflow-y-auto">
                @foreach (session('import_errors') as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- BUAT QUIZ --}}
    <section class="p-6 rounded-xl bg-white border border-slate-200/70 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-patch-question text-xl"></i></div>
                <div>
                    <h2 class="font-bold text-slate-900 text-lg">Buat Quiz dari Template</h2>
                    <p class="text-sm text-slate-500">1) Unduh template → 2) isi soal → 3) unggah di formulir ini.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('lms.guru.quiz.template', 'xlsx') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-sm font-semibold">
                    <i class="bi bi-file-earmark-excel-fill"></i>Template Excel
                </a>
                <a href="{{ route('lms.guru.quiz.template', 'docx') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm font-semibold">
                    <i class="bi bi-file-earmark-word-fill"></i>Template Word
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('lms.guru.quiz.store', $pengampuMapel) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 space-y-1">
                    <label class="text-sm font-semibold text-slate-800">Judul Quiz <span class="text-rose-500">*</span></label>
                    <input name="judul" required maxlength="150" value="{{ old('judul') }}" placeholder="Contoh: Quiz 1 — Dasar HTML & CSS"
                        class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-800">Durasi (menit) <span class="text-rose-500">*</span></label>
                    <input name="durasi_menit" type="number" min="1" max="300" required value="{{ old('durasi_menit', 30) }}"
                        class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-800">Petunjuk untuk siswa <span class="text-slate-400 font-normal">(opsional)</span></label>
                <textarea name="deskripsi" rows="2" maxlength="1000" class="w-full p-3 rounded-lg bg-slate-100 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-800">Boleh mulai sejak <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input name="mulai_pada" type="datetime-local" value="{{ old('mulai_pada') }}" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-800">Batas akhir mulai <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input name="selesai_pada" type="datetime-local" value="{{ old('selesai_pada') }}" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-800">Maks. percobaan</label>
                    <select name="maks_percobaan" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
                        @foreach ([1, 2, 3, 4, 5] as $n)
                            <option value="{{ $n }}" @selected(old('maks_percobaan', 1) == $n)>{{ $n }}× (nilai terbaik dipakai)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-700">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="acak_soal" value="1" class="rounded" @checked(old('acak_soal'))> Acak urutan soal per siswa</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="tampil_pembahasan" value="1" class="rounded" @checked(old('tampil_pembahasan'))> Tampilkan kunci &amp; pembahasan setelah selesai</label>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-800">File soal (.xlsx / .docx) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" required accept=".xlsx,.xls,.docx"
                    class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-800 file:text-white file:font-semibold file:text-sm hover:file:bg-slate-700">
                <p class="text-xs text-slate-500">Maks. 5 MB • {{ \App\Services\Lms\QuizTemplateService::MAKS_SOAL }} soal. Quiz dibuat sebagai <b>draft</b>; terbitkan setelah soal diperiksa.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm">
                    <i class="bi bi-cloud-arrow-up"></i>Impor Soal &amp; Buat Quiz
                </button>
            </div>
        </form>
    </section>

    {{-- DAFTAR --}}
    <section class="space-y-3">
        <h2 class="font-bold text-slate-900 text-lg">Daftar Quiz</h2>
        @forelse ($quiz as $q)
            <a href="{{ route('lms.guru.quiz.show', $q) }}" class="block p-4 sm:p-5 rounded-xl bg-white border border-slate-200/70 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge[$q->status][1] }}">{{ $badge[$q->status][0] }}</span>
                            <span class="text-xs text-slate-500">{{ $q->soal_count }} soal • {{ $q->durasi_menit }} menit • {{ $q->maks_percobaan }}× percobaan</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 truncate">{{ $q->judul }}</h3>
                        @if ($q->mulai_pada || $q->selesai_pada)
                            <p class="text-xs text-slate-500"><i class="bi bi-clock"></i>
                                {{ $q->mulai_pada?->format('d M Y H:i') ?? 'kapan saja' }} — {{ $q->selesai_pada?->format('d M Y H:i') ?? 'tanpa batas' }}
                            </p>
                        @endif
                    </div>
                    <div class="text-sm text-slate-600 shrink-0">
                        <b class="text-slate-900">{{ $peserta[$q->id] ?? 0 }}</b> / {{ $jumlahSiswa }} siswa selesai
                        <i class="bi bi-chevron-right ml-2 text-slate-300"></i>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center text-slate-500">Belum ada quiz. Buat yang pertama lewat formulir di atas.</div>
        @endforelse
    </section>
@endsection
