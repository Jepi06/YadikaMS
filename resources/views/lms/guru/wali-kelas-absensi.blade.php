{{-- resources/views/lms/guru/wali-kelas-absensi.blade.php
     Variabel dari WaliKelasController@absensi:
       $kelasDiwalikan, $kelas, $periodeList, $tahunAjaran, $semester,
       $hariMasukPerBulan  → Collection ['Y-m' => jumlah hari masuk]
       $totalHariMasuk
       $rekap              → array of object {siswa, per_bulan[Y-m] = [hadir, hari_masuk, persen], total_hadir, total_hari_masuk, total_persen}
       $rekapKelasPerBulan → ['Y-m' => [hari_aktif, slot, hadir, izin, sakit, alpa, persen_*]]
       $rekapKelasTotal    → [slot, hadir, izin, sakit, alpa, persen_*]
       $bulanDipilih, $tanggalList (Collection Y-m-d)
       $detailHarian       → [siswa_id][Y-m-d] = ['status' => 'Hadir', 'rincian' => 'Hadir 3, Alpa 1']
     Semua relasi sudah di-load di controller (lazy loading dimatikan).

     RESPONSIF:
       - HP & tablet (< lg) : data tampil sebagai kartu per siswa / per bulan (tanpa scroll samping)
       - PC (>= lg)         : tabel lengkap; kolom nama sticky hanya di PC, lebarnya dibatasi
--}}
@extends($layout ?? 'lms.layouts.app')

@section('title', 'Rekap Absensi Wali Kelas - LMS Yadika')
@section('breadcrumb', 'Rekap Absensi')

@push('styles')
    <style>
        /* Sembunyikan panah bawaan <details> di semua browser */
        details.kartu-siswa>summary {
            list-style: none;
        }

        details.kartu-siswa>summary::-webkit-details-marker {
            display: none;
        }

        details.kartu-siswa[open] .ikon-buka {
            transform: rotate(180deg);
        }

        .ikon-buka {
            transition: transform .2s ease;
        }
    </style>
@endpush

@section('content')
    @php
        $C = \Illuminate\Support\Carbon::class;

        $namaKelasFn = fn($k) => $k->nama_kelas ?? ($k->nama ?? '-');
        $namaKelas = $namaKelasFn($kelas);
        $namaSiswa = fn($s) => $s->nama ?? ($s->name ?? '-');
        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))
            ->take(2)
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');

        $bulanPanjang = fn($ym) => $C::createFromFormat('Y-m-d', $ym . '-01')->translatedFormat('F Y');
        $bulanPendek = fn($ym) => $C::createFromFormat('Y-m-d', $ym . '-01')->translatedFormat('M Y');

        $daftarTahun = $periodeList->pluck('tahun_ajaran')->push($tahunAjaran)->filter()->unique()->values();
        $daftarSemester = $periodeList->pluck('semester')->push($semester)->filter()->unique()->values();

        $statusStyle = [
            'Hadir' => ['H', 'bg-emerald-100 text-emerald-800'],
            'Izin' => ['I', 'bg-blue-100 text-blue-800'],
            'Sakit' => ['S', 'bg-amber-100 text-amber-800'],
            'Alpa' => ['A', 'bg-rose-100 text-rose-800'],
        ];

        $warnaPersen = fn($p) => $p >= 90 ? 'text-emerald-700' : ($p >= 75 ? 'text-amber-700' : 'text-rose-700');
        $warnaBar = fn($p) => $p >= 90 ? 'bg-emerald-500' : ($p >= 75 ? 'bg-amber-500' : 'bg-rose-500');
        $labelPersen = function ($p) {
            if ($p >= 100) {
                return ['Sempurna', 'bg-emerald-100 text-emerald-800'];
            }
            if ($p >= 90) {
                return ['Aman', 'bg-emerald-100 text-emerald-800'];
            }
            if ($p >= 75) {
                return ['Waspada', 'bg-amber-100 text-amber-800'];
            }
            return ['Kritis', 'bg-rose-100 text-rose-800'];
        };

        $ringkasHarian = function ($siswaId) use ($detailHarian, $tanggalList) {
            $c = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
            foreach ($tanggalList as $t) {
                $st = $detailHarian[$siswaId][$t]['status'] ?? null;
                if ($st && isset($c[$st])) {
                    $c[$st]++;
                }
            }
            return $c;
        };

        // Carbon per tanggal dihitung sekali (dipakai header tabel & kalender HP)
        $tanggalInfo = $tanggalList->mapWithKeys(fn($t) => [$t => $C::parse($t)]);

        $rekapUrut = collect($rekap)->sortBy(fn($r) => strtolower($namaSiswa($r->siswa)))->values();
        $jumlahSiswa = $rekapUrut->count();
        $adaData = $hariMasukPerBulan->isNotEmpty();

        $nKritis = $rekapUrut->filter(fn($r) => $r->total_hari_masuk > 0 && $r->total_persen < 75)->count();
        $nSempurna = $rekapUrut->filter(fn($r) => $r->total_hari_masuk > 0 && $r->total_persen >= 100)->count();
        $persenSempurna = $jumlahSiswa > 0 ? round(($nSempurna / $jumlahSiswa) * 100, 1) : 0;
        $persenHadirKelas = $rekapKelasTotal['persen_hadir'] ?? 0;
        $hariBulanDipilih = $bulanDipilih ? $hariMasukPerBulan[$bulanDipilih] ?? 0 : 0;

        $paramPeriode = ['kelas_id' => $kelas->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester];
        $modeAdmin = $modeAdmin ?? false;
        $formAction = $modeAdmin ? route('admin.rekap-absensi.index') : route('lms.guru.wali-kelas.absensi');
        $urlDashboard = $modeAdmin ? route('admin.dashboard') : route('lms.guru.dashboard');
        $urlNilai = $modeAdmin ? null : route('lms.guru.wali-kelas.index', $paramPeriode);
        $labelHeader = $modeAdmin ? 'Rekap Absensi — Semua Kelas' : 'Rekap Absensi — Wali Kelas';
    @endphp

    {{-- HEADER --}}
    <section
        class="bg-white border border-slate-200/70 rounded-xl p-4 sm:p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500 font-medium">
                <a href="{{ $urlDashboard }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @if ($urlNilai)
                    <a href="{{ $urlNilai }}" class="hover:text-blue-600 transition-colors">Wali Kelas</a>
                    <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @endif
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold">{{ $namaKelas }}</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300 hidden sm:inline"></i>
                <span class="text-slate-800 font-semibold hidden sm:inline">Rekap Absensi</span>
            </nav>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 pt-0.5">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Rekap Absensi — Wali Kelas</h1>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                    {{ $namaKelas }} • Semester {{ $semester }} {{ $tahunAjaran }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 w-full lg:w-auto">
            <a href="{{ route('lms.guru.wali-kelas.index', $paramPeriode) }}"
                class="inline-flex items-center justify-center gap-2 w-full lg:w-auto px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                <i class="bi bi-award"></i><span>Lihat Rekap Nilai</span>
            </a>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-4 sm:p-5 shadow-sm">
        <form id="filterAbsensi" method="GET" action="{{ route('lms.guru.wali-kelas.absensi') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="space-y-1.5 min-w-0">
                <label for="fKelas" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i
                        class="bi bi-people text-blue-600"></i>Rombongan Belajar (Kelas)</label>
                {{-- text-base di HP supaya iOS tidak zoom otomatis saat fokus --}}
                <select id="fKelas" name="kelas_id"
                    class="w-full h-11 sm:h-10 px-3 rounded-lg bg-slate-100 text-base sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($kelasDiwalikan as $k)
                        <option value="{{ $k->id }}" @selected($k->id === $kelas->id)>{{ $namaKelasFn($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5 min-w-0">
                <label for="fTahun" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i
                        class="bi bi-calendar3 text-blue-600"></i>Tahun Ajaran</label>
                <select id="fTahun" name="tahun_ajaran"
                    class="w-full h-11 sm:h-10 px-3 rounded-lg bg-slate-100 text-base sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($daftarTahun as $th)
                        <option value="{{ $th }}" @selected($th == $tahunAjaran)>{{ $th }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5 min-w-0">
                <label for="fSemester" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i
                        class="bi bi-hourglass-split text-blue-600"></i>Semester</label>
                <select id="fSemester" name="semester"
                    class="w-full h-11 sm:h-10 px-3 rounded-lg bg-slate-100 text-base sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($daftarSemester as $sm)
                        <option value="{{ $sm }}" @selected($sm == $semester)>{{ $sm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5 min-w-0">
                <label for="fBulan" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i
                        class="bi bi-calendar-event text-blue-600"></i>Bulan (Detail Harian)</label>
                <select id="fBulan" name="bulan" @disabled(!$adaData)
                    class="w-full h-11 sm:h-10 px-3 rounded-lg bg-slate-100 text-base sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 disabled:opacity-60">
                    @forelse ($hariMasukPerBulan as $ym => $hari)
                        <option value="{{ $ym }}" @selected($ym === $bulanDipilih)>{{ $bulanPanjang($ym) }}
                            ({{ $hari }} hari masuk)
                        </option>
                    @empty
                        <option value="">Belum ada data</option>
                    @endforelse
                </select>
            </div>
        </form>
    </section>

    @if (!$adaData)
        <section class="p-8 sm:p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center space-y-2">
            <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400"><i
                    class="bi bi-calendar-x text-xl"></i></div>
            <p class="font-semibold text-slate-800">Belum ada data presensi</p>
            <p class="text-sm text-slate-500">Belum ada presensi tercatat untuk {{ $namaKelas }} pada semester
                {{ $semester }} {{ $tahunAjaran }}.</p>
        </section>
    @else
        {{-- KPI (2 kolom di HP, 4 kolom di desktop) --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div
                class="bg-white border border-slate-200/70 rounded-xl p-3 sm:p-4 shadow-sm flex items-center justify-between gap-2">
                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                    <p
                        class="text-[10px] sm:text-[11px] uppercase tracking-wider text-slate-500 font-semibold leading-tight">
                        Rata-rata Kehadiran</p>
                    <p class="text-2xl sm:text-3xl font-bold {{ $warnaPersen($persenHadirKelas) }}">
                        {{ $persenHadirKelas }}%</p>
                    <span class="text-[11px] sm:text-xs text-slate-500">Target minimal 90%</span>
                </div>
                <div
                    class="hidden sm:flex w-12 h-12 rounded-xl bg-emerald-50 items-center justify-center text-emerald-600 shrink-0">
                    <i class="bi bi-patch-check-fill text-2xl"></i>
                </div>
            </div>
            <div
                class="bg-white border border-slate-200/70 rounded-xl p-3 sm:p-4 shadow-sm flex items-center justify-between gap-2">
                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                    <p
                        class="text-[10px] sm:text-[11px] uppercase tracking-wider text-slate-500 font-semibold leading-tight">
                        Total Hari Masuk</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $totalHariMasuk }} <span
                            class="text-xs sm:text-sm font-normal text-slate-500">hari</span></p>
                    <span class="text-[11px] sm:text-xs text-slate-500">{{ $jumlahSiswa }} siswa aktif</span>
                </div>
                <div
                    class="hidden sm:flex w-12 h-12 rounded-xl bg-blue-50 items-center justify-center text-blue-600 shrink-0">
                    <i class="bi bi-calendar-check text-2xl"></i>
                </div>
            </div>
            <div
                class="bg-white border border-slate-200/70 rounded-xl p-3 sm:p-4 shadow-sm flex items-center justify-between gap-2">
                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                    <p
                        class="text-[10px] sm:text-[11px] uppercase tracking-wider text-slate-500 font-semibold leading-tight">
                        Kehadiran Kritis (&lt;75%)</p>
                    <p class="text-2xl sm:text-3xl font-bold {{ $nKritis > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $nKritis }} <span class="text-xs sm:text-sm font-normal text-slate-500">siswa</span></p>
                    @if ($nKritis > 0)
                        <span class="text-[11px] sm:text-xs text-rose-600 font-semibold">Perlu tindak lanjut</span>
                    @else
                        <span class="text-[11px] sm:text-xs text-emerald-600 font-semibold">Tidak ada siswa kritis</span>
                    @endif
                </div>
                <div
                    class="hidden sm:flex w-12 h-12 rounded-xl bg-rose-50 items-center justify-center text-rose-600 shrink-0">
                    <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                </div>
            </div>
            <div
                class="bg-white border border-slate-200/70 rounded-xl p-3 sm:p-4 shadow-sm flex items-center justify-between gap-2">
                <div class="space-y-0.5 sm:space-y-1 min-w-0">
                    <p
                        class="text-[10px] sm:text-[11px] uppercase tracking-wider text-slate-500 font-semibold leading-tight">
                        Presensi Sempurna (100%)</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $nSempurna }} <span
                            class="text-xs sm:text-sm font-normal text-slate-500">siswa</span></p>
                    <span class="text-[11px] sm:text-xs text-blue-700 font-semibold">{{ $persenSempurna }}% dari
                        kelas</span>
                </div>
                <div
                    class="hidden sm:flex w-12 h-12 rounded-xl bg-slate-100 items-center justify-center text-blue-700 shrink-0">
                    <i class="bi bi-trophy-fill text-2xl"></i>
                </div>
            </div>
        </section>

        {{-- ================= DETAIL HARIAN ================= --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-4 sm:p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i
                            class="bi bi-calendar3-week text-xl"></i></div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-slate-900 text-base sm:text-lg">Detail Harian —
                            {{ $bulanDipilih ? $bulanPanjang($bulanDipilih) : '-' }}</h2>
                        <p class="text-xs sm:text-sm text-slate-500">Status presensi harian siswa {{ $namaKelas }}
                            ({{ $hariBulanDipilih }} hari masuk)</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] font-semibold">
                    <span class="text-slate-500">Legenda:</span>
                    <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">H Hadir</span>
                    <span class="px-2 py-1 rounded bg-blue-50 text-blue-800 border border-blue-200">I Izin</span>
                    <span class="px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200">S Sakit</span>
                    <span class="px-2 py-1 rounded bg-rose-50 text-rose-800 border border-rose-200">A Alpa</span>
                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">– Tidak ada
                        data</span>
                </div>
            </div>

            {{-- HP & tablet: kartu per siswa, ketuk untuk lihat kalender harian --}}
            <div class="lg:hidden space-y-2.5">
                @foreach ($rekapUrut as $r)
                    @php
                        $sid = $r->siswa->id;
                        $nama = $namaSiswa($r->siswa);
                        $sum = $ringkasHarian($sid);
                    @endphp
                    <details
                        class="kartu-siswa rounded-xl border {{ $sum['Alpa'] > 0 ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200 bg-white' }}">
                        <summary class="cursor-pointer p-3 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ $inisial($nama) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm text-slate-900 leading-tight break-words">
                                    {{ $nama }}</div>
                                <div class="flex flex-wrap gap-1 mt-1 text-[11px] font-bold">
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">H
                                        {{ $sum['Hadir'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">I
                                        {{ $sum['Izin'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">S
                                        {{ $sum['Sakit'] }}</span>
                                    <span
                                        class="px-1.5 py-0.5 rounded {{ $sum['Alpa'] > 0 ? 'bg-rose-200 text-rose-800' : 'bg-rose-100 text-rose-800' }}">A
                                        {{ $sum['Alpa'] }}</span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down ikon-buka text-slate-400 shrink-0"></i>
                        </summary>
                        <div class="px-3 pb-3 pt-3 border-t border-slate-100">
                            <p class="text-[11px] text-slate-400 mb-2">Angka kecil = tanggal, huruf = status presensi.</p>
                            <div class="grid grid-cols-7 gap-1.5">
                                @foreach ($tanggalList as $tgl)
                                    @php
                                        $cell = $detailHarian[$sid][$tgl] ?? null;
                                        $st = $cell['status'] ?? null;
                                        [$huruf, $kls] = $statusStyle[$st] ?? [null, null];
                                    @endphp
                                    <div class="text-center">
                                        <div class="text-[10px] text-slate-400 leading-none mb-0.5">
                                            {{ $tanggalInfo[$tgl]->format('d') }}</div>
                                        <span
                                            class="block w-full py-1 rounded text-xs font-bold {{ $kls ?? 'text-slate-300 bg-slate-50' }}">{{ $huruf ?? '–' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>

            {{-- PC: matriks tanggal (kolom nama sticky hanya di lg ke atas) --}}
            <div class="hidden lg:block overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm"
                    style="min-width: {{ 260 + $tanggalList->count() * 40 + 160 }}px">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200">
                            <th
                                class="sticky left-0 z-20 bg-slate-50 px-4 py-3 w-[240px] min-w-[240px] max-w-[240px] border-r border-slate-200 text-xs uppercase tracking-wider">
                                Nama Siswa</th>
                            @foreach ($tanggalList as $tgl)
                                @php $d = $tanggalInfo[$tgl]; @endphp
                                <th class="px-1 py-2 text-center border-r border-slate-100">
                                    <div class="text-slate-400 font-normal">{{ $d->translatedFormat('D') }}</div>
                                    <div class="text-slate-800 font-bold">{{ $d->format('d') }}</div>
                                </th>
                            @endforeach
                            <th class="px-2 py-2 text-center bg-emerald-50 text-emerald-800 border-l border-emerald-100">H
                            </th>
                            <th class="px-2 py-2 text-center bg-blue-50 text-blue-800">I</th>
                            <th class="px-2 py-2 text-center bg-amber-50 text-amber-800">S</th>
                            <th class="px-2 py-2 text-center bg-rose-50 text-rose-800">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapUrut as $r)
                            @php
                                $sid = $r->siswa->id;
                                $nama = $namaSiswa($r->siswa);
                                $sum = $ringkasHarian($sid);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors {{ $sum['Alpa'] > 0 ? 'bg-rose-50/30' : '' }}">
                                <td
                                    class="sticky left-0 z-10 bg-white px-4 py-2.5 w-[240px] min-w-[240px] max-w-[240px] border-r border-slate-200">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold text-[11px] flex items-center justify-center shrink-0">
                                            {{ $inisial($nama) }}</div>
                                        <span
                                            class="font-medium text-slate-900 leading-tight break-words min-w-0">{{ $nama }}</span>
                                    </div>
                                </td>
                                @foreach ($tanggalList as $tgl)
                                    @php
                                        $cell = $detailHarian[$sid][$tgl] ?? null;
                                        $st = $cell['status'] ?? null;
                                        [$huruf, $kls] = $statusStyle[$st] ?? [null, null];
                                    @endphp
                                    <td class="p-1 text-center">
                                        @if ($huruf)
                                            <span title="{{ $cell['rincian'] ?? $st }}"
                                                class="inline-block w-6 py-0.5 rounded text-xs font-bold {{ $kls }}">{{ $huruf }}</span>
                                        @else
                                            <span class="inline-block w-6 py-0.5 rounded text-xs text-slate-300">–</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td
                                    class="px-2 py-2 text-center font-semibold bg-emerald-50/50 text-emerald-800 border-l border-emerald-100">
                                    {{ $sum['Hadir'] }}</td>
                                <td class="px-2 py-2 text-center font-semibold bg-blue-50/50 text-blue-800">
                                    {{ $sum['Izin'] }}</td>
                                <td class="px-2 py-2 text-center font-semibold bg-amber-50/50 text-amber-800">
                                    {{ $sum['Sakit'] }}</td>
                                <td
                                    class="px-2 py-2 text-center font-semibold {{ $sum['Alpa'] > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-rose-50/50 text-rose-800' }}">
                                    {{ $sum['Alpa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-start gap-3 p-3 sm:p-4 rounded-xl bg-blue-50 border border-blue-100">
                <i class="bi bi-info-circle text-blue-700 mt-0.5"></i>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    <span class="font-semibold text-slate-900">Aturan absensi harian:</span> jika dalam satu hari siswa
                    punya status berbeda di beberapa mapel,
                    status terberat yang ditampilkan (<span class="text-rose-700 font-semibold">Alpa</span> &gt; <span
                        class="text-amber-700 font-semibold">Sakit</span> &gt;
                    <span class="text-blue-700 font-semibold">Izin</span> &gt; <span
                        class="text-emerald-700 font-semibold">Hadir</span>).
                    <span class="hidden lg:inline">Arahkan kursor ke sel untuk melihat rincian per mapel.</span>
                </p>
            </div>
        </section>

        {{-- ================= REKAP PERSENTASE PER SISWA ================= --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-4 sm:p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i
                            class="bi bi-bar-chart-line text-xl"></i></div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-slate-900 text-base sm:text-lg">Rekap Persentase Per Siswa (Semester
                            {{ $semester }})</h2>
                        <p class="text-xs sm:text-sm text-slate-500">Tingkat kehadiran per bulan dan kumulatif semester</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] font-semibold">
                    <span class="text-slate-500">Status:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Aman (≥90%)</span>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800">Waspada (75–89%)</span>
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800">Kritis (&lt;75%)</span>
                </div>
            </div>

            {{-- HP & tablet: kartu per siswa --}}
            <div class="lg:hidden space-y-2.5">
                @foreach ($rekapUrut as $r)
                    @php
                        $nama = $namaSiswa($r->siswa);
                        $adaHari = $r->total_hari_masuk > 0;
                        [$lbl, $lblKls] = $adaHari
                            ? $labelPersen($r->total_persen)
                            : ['-', 'bg-slate-100 text-slate-500'];
                        $kritis = $adaHari && $r->total_persen < 75;
                    @endphp
                    <div
                        class="rounded-xl border p-3 {{ $kritis ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-sm text-slate-900 leading-tight break-words">
                                    {{ $nama }}</div>
                                @if ($r->siswa->nisn ?? null)
                                    <div class="text-[11px] font-mono text-slate-400">NISN: {{ $r->siswa->nisn }}</div>
                                @endif
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 {{ $lblKls }}">{{ $lbl }}</span>
                        </div>

                        <div class="mt-3 flex items-baseline justify-between gap-2">
                            <div>
                                <span
                                    class="text-2xl font-bold {{ $warnaPersen($r->total_persen) }}">{{ $r->total_persen }}%</span>
                                <span class="text-[11px] text-slate-400 ml-1">total semester</span>
                            </div>
                            <span
                                class="text-xs font-mono text-slate-400">{{ $r->total_hadir }}/{{ $r->total_hari_masuk }}
                                hari</span>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $warnaBar($r->total_persen) }}"
                                style="width: {{ min(100, max(0, $r->total_persen)) }}%"></div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($hariMasukPerBulan as $ym => $hari)
                                @php $pb = $r->per_bulan[$ym] ?? ['hadir' => 0, 'hari_masuk' => $hari, 'persen' => 0]; @endphp
                                <div class="rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1.5">
                                    <div class="text-[11px] text-slate-500">{{ $bulanPendek($ym) }}</div>
                                    <div class="font-bold text-sm {{ $warnaPersen($pb['persen']) }}">
                                        {{ $pb['persen'] }}%
                                        <span
                                            class="text-[11px] font-mono font-normal text-slate-400">{{ $pb['hadir'] }}/{{ $pb['hari_masuk'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PC: tabel (kolom nama sticky hanya di lg ke atas) --}}
            <div class="hidden lg:block overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm"
                    style="min-width: {{ 260 + $hariMasukPerBulan->count() * 120 + 260 }}px">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                            <th
                                class="sticky left-0 z-20 bg-slate-50 px-4 py-3 w-[260px] min-w-[260px] max-w-[260px] border-r border-slate-200 text-xs uppercase tracking-wider">
                                Identitas Siswa</th>
                            @foreach ($hariMasukPerBulan as $ym => $hari)
                                <th class="px-3 py-3 text-center border-r border-slate-100">
                                    <div class="font-bold text-slate-800">{{ $bulanPendek($ym) }}</div>
                                    <div class="text-[11px] text-slate-400 font-normal">{{ $hari }} hari masuk
                                    </div>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center bg-blue-50 border-l border-blue-200 text-blue-700">
                                <div class="font-bold">Total Semester</div>
                                <div class="text-[11px] font-normal">{{ $totalHariMasuk }} hari masuk</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapUrut as $r)
                            @php
                                $nama = $namaSiswa($r->siswa);
                                [$lbl, $lblKls] =
                                    $r->total_hari_masuk > 0
                                        ? $labelPersen($r->total_persen)
                                        : ['-', 'bg-slate-100 text-slate-500'];
                            @endphp
                            <tr
                                class="hover:bg-slate-50 transition-colors {{ $r->total_hari_masuk > 0 && $r->total_persen < 75 ? 'bg-rose-50/30' : '' }}">
                                <td
                                    class="sticky left-0 z-10 bg-white px-4 py-3 w-[260px] min-w-[260px] max-w-[260px] border-r border-slate-200">
                                    <div class="font-semibold text-slate-900 leading-tight break-words">
                                        {{ $nama }}</div>
                                    @if ($r->siswa->nisn ?? null)
                                        <div class="text-xs font-mono text-slate-400">NISN: {{ $r->siswa->nisn }}</div>
                                    @endif
                                </td>
                                @foreach ($hariMasukPerBulan as $ym => $hari)
                                    @php $pb = $r->per_bulan[$ym] ?? ['hadir' => 0, 'hari_masuk' => $hari, 'persen' => 0]; @endphp
                                    <td class="px-3 py-3 text-center border-r border-slate-100">
                                        <div class="font-bold {{ $warnaPersen($pb['persen']) }}">{{ $pb['persen'] }}%
                                        </div>
                                        <div class="text-xs font-mono text-slate-400">
                                            {{ $pb['hadir'] }}/{{ $pb['hari_masuk'] }}</div>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 bg-blue-50/50 border-l border-blue-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-bold text-base {{ $warnaPersen($r->total_persen) }}">
                                                {{ $r->total_persen }}%</div>
                                            <div class="text-xs font-mono text-slate-400">
                                                {{ $r->total_hadir }}/{{ $r->total_hari_masuk }} hari</div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $lblKls }}">{{ $lbl }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ================= REKAP AGREGAT KELAS ================= --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-4 sm:p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i
                            class="bi bi-clipboard-data text-xl"></i></div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-slate-900 text-base sm:text-lg">Rekap Agregat Kelas
                            ({{ $namaKelas }})</h2>
                        <p class="text-xs sm:text-sm text-slate-500">Total frekuensi status presensi seluruh siswa per
                            bulan</p>
                    </div>
                </div>
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-blue-700 text-xs font-semibold self-start sm:self-auto">
                    <i class="bi bi-people"></i>Jumlah Siswa: {{ $jumlahSiswa }}
                </span>
            </div>

            {{-- HP & tablet: kartu per bulan + kartu total --}}
            <div class="lg:hidden space-y-2.5">
                @foreach ($rekapKelasPerBulan as $ym => $rk)
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-semibold text-sm text-slate-900">
                                    {{ $bulanPanjang($ym) }}
                                    @if ($ym === $bulanDipilih)
                                        <span class="text-[11px] font-normal text-blue-600">(dipilih)</span>
                                    @endif
                                </div>
                                <div class="text-[11px] font-mono text-slate-400">{{ $rk['hari_aktif'] }} hari ×
                                    {{ $jumlahSiswa }} siswa = {{ number_format($rk['slot']) }} slot</div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-50 border border-slate-200 shrink-0 {{ $warnaPersen($rk['persen_hadir']) }}">{{ $rk['persen_hadir'] }}%</span>
                        </div>
                        <div class="mt-3 grid grid-cols-4 gap-1.5 text-center">
                            <div class="rounded-lg bg-emerald-50 py-1.5">
                                <div class="text-[10px] font-semibold text-emerald-700">Hadir</div>
                                <div class="font-bold text-sm text-emerald-800">{{ number_format($rk['hadir']) }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rk['persen_hadir'] }}%</div>
                            </div>
                            <div class="rounded-lg bg-blue-50 py-1.5">
                                <div class="text-[10px] font-semibold text-blue-700">Izin</div>
                                <div class="font-bold text-sm text-blue-800">{{ number_format($rk['izin']) }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rk['persen_izin'] }}%</div>
                            </div>
                            <div class="rounded-lg bg-amber-50 py-1.5">
                                <div class="text-[10px] font-semibold text-amber-700">Sakit</div>
                                <div class="font-bold text-sm text-amber-800">{{ number_format($rk['sakit']) }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rk['persen_sakit'] }}%</div>
                            </div>
                            <div class="rounded-lg bg-rose-50 py-1.5">
                                <div class="text-[10px] font-semibold text-rose-700">Alpa</div>
                                <div class="font-bold text-sm text-rose-800">{{ number_format($rk['alpa']) }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rk['persen_alpa'] }}%</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Total semester --}}
                <div class="rounded-xl bg-blue-600 text-white p-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-sm">Keseluruhan Semester</div>
                            <div class="text-[11px] font-mono text-blue-100">{{ $totalHariMasuk }} hari ×
                                {{ $jumlahSiswa }} siswa = {{ number_format($rekapKelasTotal['slot']) }} slot</div>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-md text-xs font-bold bg-white text-blue-700 shrink-0">{{ $rekapKelasTotal['persen_hadir'] }}%</span>
                    </div>
                    <div class="mt-3 grid grid-cols-4 gap-1.5 text-center">
                        <div class="rounded-lg bg-white/15 py-1.5">
                            <div class="text-[10px] font-semibold text-blue-100">Hadir</div>
                            <div class="font-bold text-sm">{{ number_format($rekapKelasTotal['hadir']) }}</div>
                            <div class="text-[10px] text-blue-100">{{ $rekapKelasTotal['persen_hadir'] }}%</div>
                        </div>
                        <div class="rounded-lg bg-white/15 py-1.5">
                            <div class="text-[10px] font-semibold text-blue-100">Izin</div>
                            <div class="font-bold text-sm">{{ number_format($rekapKelasTotal['izin']) }}</div>
                            <div class="text-[10px] text-blue-100">{{ $rekapKelasTotal['persen_izin'] }}%</div>
                        </div>
                        <div class="rounded-lg bg-white/15 py-1.5">
                            <div class="text-[10px] font-semibold text-blue-100">Sakit</div>
                            <div class="font-bold text-sm">{{ number_format($rekapKelasTotal['sakit']) }}</div>
                            <div class="text-[10px] text-blue-100">{{ $rekapKelasTotal['persen_sakit'] }}%</div>
                        </div>
                        <div class="rounded-lg bg-white/15 py-1.5">
                            <div class="text-[10px] font-semibold text-blue-100">Alpa</div>
                            <div class="font-bold text-sm text-rose-200">{{ number_format($rekapKelasTotal['alpa']) }}
                            </div>
                            <div class="text-[10px] text-blue-100">{{ $rekapKelasTotal['persen_alpa'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PC: tabel --}}
            <div class="hidden lg:block overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-4 py-3 min-w-[220px]">Periode Bulan</th>
                            <th class="px-4 py-3 text-center">Hadir (H)</th>
                            <th class="px-4 py-3 text-center">Izin (I)</th>
                            <th class="px-4 py-3 text-center">Sakit (S)</th>
                            <th class="px-4 py-3 text-center">Alpa (A)</th>
                            <th class="px-4 py-3 text-right">Tingkat Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapKelasPerBulan as $ym => $rk)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $bulanPanjang($ym) }}@if ($ym === $bulanDipilih)
                                            <span class="text-xs font-normal text-blue-600">(dipilih)</span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-mono text-slate-400">{{ $rk['hari_aktif'] }} hari ×
                                        {{ $jumlahSiswa }} siswa = {{ number_format($rk['slot']) }} slot</div>
                                </td>
                                <td class="px-4 py-3 text-center"><span
                                        class="font-semibold text-emerald-800">{{ number_format($rk['hadir']) }}</span>
                                    <span class="text-xs text-slate-400">({{ $rk['persen_hadir'] }}%)</span>
                                </td>
                                <td class="px-4 py-3 text-center"><span
                                        class="font-semibold text-blue-800">{{ number_format($rk['izin']) }}</span> <span
                                        class="text-xs text-slate-400">({{ $rk['persen_izin'] }}%)</span></td>
                                <td class="px-4 py-3 text-center"><span
                                        class="font-semibold text-amber-800">{{ number_format($rk['sakit']) }}</span>
                                    <span class="text-xs text-slate-400">({{ $rk['persen_sakit'] }}%)</span>
                                </td>
                                <td class="px-4 py-3 text-center"><span
                                        class="font-semibold text-rose-800">{{ number_format($rk['alpa']) }}</span> <span
                                        class="text-xs text-slate-400">({{ $rk['persen_alpa'] }}%)</span></td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="inline-block px-2.5 py-1 rounded-md text-xs font-bold bg-slate-50 border border-slate-200 {{ $warnaPersen($rk['persen_hadir']) }}">{{ $rk['persen_hadir'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-blue-600 text-white">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-base">Keseluruhan Semester</div>
                                <div class="text-xs font-mono text-blue-100">{{ $totalHariMasuk }} hari ×
                                    {{ $jumlahSiswa }} siswa = {{ number_format($rekapKelasTotal['slot']) }} slot</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-bold text-base">{{ number_format($rekapKelasTotal['hadir']) }}</div>
                                <div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_hadir'] }}%)</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-bold text-base">{{ number_format($rekapKelasTotal['izin']) }}</div>
                                <div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_izin'] }}%)</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-bold text-base">{{ number_format($rekapKelasTotal['sakit']) }}</div>
                                <div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_sakit'] }}%)</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-bold text-base text-rose-200">
                                    {{ number_format($rekapKelasTotal['alpa']) }}</div>
                                <div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_alpa'] }}%)</div>
                            </td>
                            <td class="px-4 py-3.5 text-right"><span
                                    class="inline-block px-3 py-1 rounded-md text-sm font-bold bg-white text-blue-700 shadow-sm">{{ $rekapKelasTotal['persen_hadir'] }}%</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-slate-50 border border-slate-200/70 rounded-xl p-3 sm:p-4 flex items-start gap-3">
            <i class="bi bi-calculator text-slate-400 mt-0.5"></i>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-900">Catatan perhitungan:</span>
                persentase per siswa = <span class="font-mono text-slate-800">hari hadir ÷ hari masuk</span> (hari masuk =
                tanggal unik yang punya presensi di mapel mana pun).
                Persentase kelas = <span class="font-mono text-slate-800">total kejadian status ÷ (hari masuk × jumlah
                    siswa)</span>.
                Data diambil dari presensi seluruh mapel di kelas ini pada periode terpilih.
            </p>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        (function() {
            const form = document.getElementById('filterAbsensi');
            if (!form) return;
            const f = id => document.getElementById(id);

            // Ganti kelas → reset periode & bulan (periodenya bisa beda per kelas)
            f('fKelas')?.addEventListener('change', () => {
                ['fTahun', 'fSemester', 'fBulan'].forEach(i => f(i) && (f(i).disabled = true));
                form.submit();
            });
            // Ganti periode → reset bulan
            ['fTahun', 'fSemester'].forEach(i => f(i)?.addEventListener('change', () => {
                f('fBulan') && (f('fBulan').disabled = true);
                form.submit();
            }));
            f('fBulan')?.addEventListener('change', () => form.submit());
        })();
    </script>
@endpush
