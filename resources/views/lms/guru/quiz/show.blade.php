{{-- Variabel: $quiz (soal dimuat), $pengampuMapel, $hasil, $sudahAdaPercobaan, $rata, $tertinggi, $terendah, $totalPoin --}}
@extends('lms.layouts.app')

@section('title', 'Detail Quiz - LMS Yadika')
@section('breadcrumb', 'Detail Quiz')

@section('content')
    @php
        $namaMapel = $pengampuMapel->mataPelajaran->nama ?? 'Mata Pelajaran';
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? '-';
        $badge = ['draft' => ['Draft', 'bg-slate-100 text-slate-600'], 'terbit' => ['Terbit', 'bg-emerald-50 text-emerald-700'], 'ditutup' => ['Ditutup', 'bg-amber-50 text-amber-700']][$quiz->status];
        $dt = fn($d) => $d?->format('Y-m-d\TH:i');
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <a href="{{ route('lms.guru.quiz.index', $pengampuMapel) }}" class="text-xs text-blue-600 font-semibold"><i class="bi bi-arrow-left"></i> Daftar quiz {{ $namaMapel }} • {{ $namaKelas }}</a>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $quiz->judul }}</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge[1] }}">{{ $badge[0] }}</span>
            </div>
            <p class="text-sm text-slate-500">{{ $quiz->soal->count() }} soal • total {{ rtrim(rtrim(number_format($totalPoin, 2), '0'), '.') }} poin • {{ $quiz->durasi_menit }} menit</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($quiz->status !== 'terbit')
                <form method="POST" action="{{ route('lms.guru.quiz.status', $quiz) }}">@csrf
                    <input type="hidden" name="aksi" value="terbit">
                    <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold"><i class="bi bi-send-check"></i> {{ $quiz->status === 'ditutup' ? 'Buka Kembali' : 'Terbitkan' }}</button>
                </form>
            @endif
            @if ($quiz->status === 'terbit')
                <form method="POST" action="{{ route('lms.guru.quiz.status', $quiz) }}" onsubmit="return confirm('Tutup quiz? Siswa tidak bisa memulai lagi.')">@csrf
                    <input type="hidden" name="aksi" value="tutup">
                    <button class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold"><i class="bi bi-lock"></i> Tutup Quiz</button>
                </form>
            @endif
            @if ($quiz->status === 'terbit' && ! $sudahAdaPercobaan)
                <form method="POST" action="{{ route('lms.guru.quiz.status', $quiz) }}">@csrf
                    <input type="hidden" name="aksi" value="draft">
                    <button class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold">Tarik ke Draft</button>
                </form>
            @endif
            <form method="POST" action="{{ route('lms.guru.quiz.destroy', $quiz) }}" onsubmit="return confirm('Hapus quiz beserta semua soal dan hasil siswa?')">@csrf @method('DELETE')
                <button class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus quiz"><i class="bi bi-trash3 text-base"></i></button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex gap-2"><i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span></div>
    @endif
    @if (session('import_errors'))
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
            <div class="font-semibold mb-1">File belum bisa diimpor:</div>
            <ul class="list-disc ml-5 max-h-56 overflow-y-auto">@foreach (session('import_errors') as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    @if ($quiz->status === 'draft')
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
            <i class="bi bi-eye-slash"></i> Quiz masih <b>draft</b> — belum terlihat oleh siswa. Periksa soal di bawah lalu klik <b>Terbitkan</b>.
        </div>
    @endif
    <div class="rounded-lg bg-blue-50 border border-blue-100 text-blue-800 text-sm px-4 py-3">
        <i class="bi bi-info-circle"></i> Nilai quiz masuk ke nilai akhir sesuai opsi di halaman
        <a class="font-semibold underline" href="{{ route('lms.guru.nilai.index', $pengampuMapel) }}">Rekap Nilai</a>
        (nonaktif / termasuk bobot / nilai tambahan).
    </div>

    {{-- STATISTIK --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([['Rata-rata', $rata], ['Tertinggi', $tertinggi], ['Terendah', $terendah]] as [$l, $v])
            <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">{{ $l }}</p>
                <p class="text-3xl font-bold text-slate-900">{{ $v !== null ? rtrim(rtrim(number_format($v, 1), '0'), '.') : '–' }}</p>
            </div>
        @endforeach
        <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm">
            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Sudah selesai</p>
            <p class="text-3xl font-bold text-slate-900">{{ $hasil->whereNotNull('nilai')->count() }}<span class="text-sm font-normal text-slate-500"> / {{ $hasil->count() }}</span></p>
        </div>
    </section>

    {{-- HASIL SISWA --}}
    <section class="rounded-xl bg-white border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="p-5 font-bold text-slate-900 text-lg">Hasil Siswa</div>
        <div class="overflow-x-auto border-t border-slate-100">
            <table class="w-full text-sm text-left min-w-[640px]">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr><th class="py-3 px-5">Siswa</th><th class="px-3 text-center">Percobaan</th><th class="px-3 text-center">Benar</th><th class="px-3 text-center">Nilai terbaik</th><th class="px-3 text-center">Status</th><th class="px-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($hasil as $h)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-5"><div class="font-semibold text-slate-900">{{ $h->siswa->nama }}</div><div class="text-xs font-mono text-slate-400">{{ $h->siswa->nis }}</div></td>
                            <td class="px-3 text-center">{{ $h->jumlah }} / {{ $quiz->maks_percobaan }}</td>
                            <td class="px-3 text-center">{{ $h->benar !== null ? $h->benar . ' / ' . $quiz->soal->count() : '–' }}</td>
                            <td class="px-3 text-center font-mono font-bold {{ $h->nilai !== null && $h->nilai < 75 ? 'text-rose-600' : 'text-slate-900' }}">{{ $h->nilai !== null ? rtrim(rtrim(number_format($h->nilai, 1), '0'), '.') : '–' }}</td>
                            <td class="px-3 text-center">
                                @if ($h->berjalan)<span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-xs font-semibold">Mengerjakan</span>
                                @elseif ($h->nilai !== null)<span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Selesai</span>
                                @else<span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Belum</span>@endif
                            </td>
                            <td class="px-3 text-right">
                                @if ($h->jumlah > 0)
                                    <form method="POST" action="{{ route('lms.guru.quiz.reset', [$quiz, $h->siswa]) }}" onsubmit="return confirm('Reset percobaan {{ addslashes($h->siswa->nama) }}? Nilainya dihapus dan ia bisa mengulang.')">@csrf
                                        <button class="text-xs font-semibold text-rose-600 hover:underline">Reset</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- PENGATURAN --}}
    <section class="p-6 rounded-xl bg-white border border-slate-200/70 shadow-sm space-y-4">
        <h2 class="font-bold text-slate-900 text-lg">Pengaturan Quiz</h2>
        <form method="POST" action="{{ route('lms.guru.quiz.update', $quiz) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 space-y-1"><label class="text-sm font-semibold text-slate-800">Judul</label>
                    <input name="judul" required maxlength="150" value="{{ old('judul', $quiz->judul) }}" class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm"></div>
                <div class="space-y-1"><label class="text-sm font-semibold text-slate-800">Durasi (menit)</label>
                    <input name="durasi_menit" type="number" min="1" max="300" required value="{{ old('durasi_menit', $quiz->durasi_menit) }}" class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm"></div>
            </div>
            <div class="space-y-1"><label class="text-sm font-semibold text-slate-800">Petunjuk untuk siswa</label>
                <textarea name="deskripsi" rows="2" maxlength="1000" class="w-full p-3 rounded-lg bg-slate-100 text-sm">{{ old('deskripsi', $quiz->deskripsi) }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1"><label class="text-sm font-semibold text-slate-800">Boleh mulai sejak</label>
                    <input name="mulai_pada" type="datetime-local" value="{{ old('mulai_pada', $dt($quiz->mulai_pada)) }}" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm"></div>
                <div class="space-y-1"><label class="text-sm font-semibold text-slate-800">Batas akhir mulai</label>
                    <input name="selesai_pada" type="datetime-local" value="{{ old('selesai_pada', $dt($quiz->selesai_pada)) }}" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm"></div>
                <div class="space-y-1"><label class="text-sm font-semibold text-slate-800">Maks. percobaan</label>
                    <select name="maks_percobaan" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
                        @foreach ([1, 2, 3, 4, 5] as $n)<option value="{{ $n }}" @selected(old('maks_percobaan', $quiz->maks_percobaan) == $n)>{{ $n }}×</option>@endforeach
                    </select></div>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-700">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="acak_soal" value="1" class="rounded" @checked(old('acak_soal', $quiz->acak_soal))> Acak urutan soal</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="tampil_pembahasan" value="1" class="rounded" @checked(old('tampil_pembahasan', $quiz->tampil_pembahasan))> Tampilkan kunci &amp; pembahasan</label>
            </div>
            <button class="px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold"><i class="bi bi-save2"></i> Simpan Pengaturan</button>
        </form>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="font-semibold text-slate-900 text-sm mb-2">Ganti soal dengan file baru</h3>
            @if ($sudahAdaPercobaan)
                <p class="text-sm text-slate-500">Tidak tersedia karena sudah ada siswa yang mengerjakan. Reset percobaan mereka atau buat quiz baru.</p>
            @else
                <form method="POST" action="{{ route('lms.guru.quiz.soal', $quiz) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 sm:items-center">@csrf
                    <input type="file" name="file" required accept=".xlsx,.xls,.docx" class="text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold">
                    <button class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold" onclick="return confirm('Semua soal saat ini akan diganti. Lanjutkan?')">Ganti Soal</button>
                </form>
            @endif
        </div>
    </section>

    {{-- PRATINJAU SOAL --}}
    <section class="rounded-xl bg-white border border-slate-200/70 shadow-sm">
        <div class="p-5 font-bold text-slate-900 text-lg">Soal ({{ $quiz->soal->count() }})</div>
        <div class="divide-y divide-slate-100 border-t border-slate-100">
            @foreach ($quiz->soal as $i => $s)
                <div class="p-5 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-slate-900 whitespace-pre-line"><span class="text-slate-400">{{ $i + 1 }}.</span> {{ $s->pertanyaan }}</p>
                        <span class="shrink-0 text-xs text-slate-500">{{ rtrim(rtrim(number_format($s->poin, 2), '0'), '.') }} poin</span>
                    </div>
                    <ul class="space-y-1 text-sm">
                        @foreach ($s->opsi() as $huruf => $teks)
                            <li class="flex gap-2 px-3 py-1.5 rounded-lg {{ $huruf === $s->kunci ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-700' }}">
                                <span class="font-mono">{{ $huruf }}.</span><span class="whitespace-pre-line">{{ $teks }}</span>
                                @if ($huruf === $s->kunci)<i class="bi bi-check-circle-fill ml-auto"></i>@endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($s->pembahasan)<p class="text-xs text-slate-500 italic">Pembahasan: {{ $s->pembahasan }}</p>@endif
                </div>
            @endforeach
        </div>
    </section>
@endsection
