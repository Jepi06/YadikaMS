{{-- Variabel: $baris (quiz, berjalan, terbaik, jumlahSelesai, sisa, bisaMulai), $siswa --}}
@extends('lms.layouts.app')

@section('title', 'Quiz - LMS Yadika')
@section('breadcrumb', 'Quiz')

@section('content')
    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quiz</h1>
        <p class="text-sm text-slate-500 mt-1">Quiz dari semua mata pelajaran di kelas {{ $siswa->kelas->nama_kelas ?? '' }}. Waktu berjalan sejak kamu menekan Mulai.</p>
    </section>

    @if ($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="space-y-3">
        @forelse ($baris as $b)
            @php $q = $b->quiz; @endphp
            <div class="p-5 rounded-xl bg-white border border-slate-200/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="min-w-0 space-y-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">{{ $q->pengampuMapel->mataPelajaran->nama ?? '-' }}</span>
                    <h3 class="font-semibold text-slate-900">{{ $q->judul }}</h3>
                    <p class="text-xs text-slate-500">{{ $q->soal_count }} soal • {{ $q->durasi_menit }} menit • sisa kesempatan {{ $b->sisa }}×
                        @if ($q->selesai_pada) • buka sampai {{ $q->selesai_pada->format('d M H:i') }} @endif
                        @if ($q->mulai_pada && now()->lt($q->mulai_pada)) • dibuka {{ $q->mulai_pada->format('d M H:i') }} @endif
                    </p>
                    @if ($q->deskripsi)<p class="text-sm text-slate-600">{{ $q->deskripsi }}</p>@endif
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($b->terbaik)
                        <div class="text-right">
                            <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Nilai terbaik</div>
                            <div class="text-2xl font-bold {{ $b->terbaik->nilai < 75 ? 'text-rose-600' : 'text-emerald-600' }}">{{ rtrim(rtrim(number_format($b->terbaik->nilai, 1), '0'), '.') }}</div>
                        </div>
                    @endif
                    @if ($b->berjalan)
                        <a href="{{ route('lms.siswa.quiz.kerjakan', $b->berjalan) }}" class="px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">Lanjutkan</a>
                    @elseif ($b->bisaMulai)
                        <form method="POST" action="{{ route('lms.siswa.quiz.mulai', $q) }}"
                              onsubmit="return confirm('Mulai quiz sekarang? Waktu {{ $q->durasi_menit }} menit langsung berjalan.')">@csrf
                            <button class="px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold"><i class="bi bi-play-fill"></i> {{ $b->jumlahSelesai ? 'Ulangi' : 'Mulai' }}</button>
                        </form>
                    @elseif ($b->terbaik)
                        <a href="{{ route('lms.siswa.quiz.hasil', $b->terbaik) }}" class="px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold">Lihat Hasil</a>
                    @else
                        <span class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                            {{ $q->status === 'ditutup' || ($q->selesai_pada && now()->gt($q->selesai_pada)) ? 'Ditutup' : 'Belum dibuka' }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center text-slate-500">
                <i class="bi bi-patch-question text-3xl text-slate-300 block mb-2"></i>Belum ada quiz untuk kelasmu.
            </div>
        @endforelse
    </div>
@endsection
