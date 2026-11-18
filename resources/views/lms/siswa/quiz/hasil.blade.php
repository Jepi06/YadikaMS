{{-- Variabel: $percobaan, $quiz, $pembahasan (koleksi {soal, jawaban, benar} atau null) --}}
@extends('lms.layouts.app')

@section('title', 'Hasil Quiz - LMS Yadika')
@section('breadcrumb', 'Hasil Quiz')

@section('content')
    @php $nilai = (float) $percobaan->nilai; $lulus = $nilai >= 75; @endphp

    @if (session('status'))
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    <section class="p-8 rounded-2xl bg-white border border-slate-200/70 shadow-sm text-center space-y-2">
        <p class="text-sm text-slate-500">{{ $quiz->pengampuMapel->mataPelajaran->nama ?? '' }} • {{ $quiz->judul }}</p>
        <div class="text-6xl font-bold {{ $lulus ? 'text-emerald-600' : 'text-rose-600' }}">{{ rtrim(rtrim(number_format($nilai, 1), '0'), '.') }}</div>
        <p class="text-slate-600">{{ $percobaan->benar }} benar dari {{ $percobaan->total_soal }} soal</p>
        <p class="text-xs text-slate-400">Selesai {{ $percobaan->selesai_at?->format('d M Y H:i') }}</p>
        <div class="pt-3"><a href="{{ route('lms.siswa.quiz.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold"><i class="bi bi-arrow-left"></i> Kembali ke daftar quiz</a></div>
    </section>

    @if ($pembahasan)
        <section class="rounded-xl bg-white border border-slate-200/70 shadow-sm">
            <div class="p-5 font-bold text-slate-900 text-lg">Kunci &amp; Pembahasan</div>
            <div class="divide-y divide-slate-100 border-t border-slate-100">
                @foreach ($pembahasan as $i => $p)
                    <div class="p-5 space-y-2">
                        <p class="font-semibold text-slate-900 whitespace-pre-line">
                            <span class="text-slate-400">{{ $i + 1 }}.</span> {{ $p->soal->pertanyaan }}
                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $p->benar ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $p->benar ? 'Benar' : ($p->jawaban ? 'Salah' : 'Tidak dijawab') }}</span>
                        </p>
                        <ul class="space-y-1 text-sm">
                            @foreach ($p->soal->opsi() as $huruf => $teks)
                                <li class="flex gap-2 px-3 py-1.5 rounded-lg
                                    {{ $huruf === $p->soal->kunci ? 'bg-emerald-50 text-emerald-800 font-semibold' : ($huruf === $p->jawaban ? 'bg-rose-50 text-rose-700' : 'text-slate-700') }}">
                                    <span class="font-mono">{{ $huruf }}.</span><span class="whitespace-pre-line">{{ $teks }}</span>
                                    @if ($huruf === $p->soal->kunci)<span class="ml-auto text-xs">kunci</span>@elseif($huruf === $p->jawaban)<span class="ml-auto text-xs">jawabanmu</span>@endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($p->soal->pembahasan)<p class="text-xs text-slate-500 italic">Pembahasan: {{ $p->soal->pembahasan }}</p>@endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
