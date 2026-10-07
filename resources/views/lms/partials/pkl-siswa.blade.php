{{-- resources/views/lms/partials/pkl-siswa.blade.php --}}
{{-- Banner status PKL untuk siswa di LMS. Data $pklSiswa dari view composer (AppServiceProvider). --}}
@php
    $info = $pklSiswa ?? null;

    $tema = [
        'belum'    => ['border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50', 'bg-amber-500 shadow-amber-500/30', 'bi-bell-fill', 'text-amber-700'],
        'ditolak'  => ['border-rose-200 bg-gradient-to-r from-rose-50 to-red-50', 'bg-rose-500 shadow-rose-500/30', 'bi-x-octagon-fill', 'text-rose-700'],
        'draft'    => ['border-sky-200 bg-gradient-to-r from-sky-50 to-blue-50', 'bg-sky-500 shadow-sky-500/30', 'bi-hourglass-split', 'text-sky-700'],
        'diajukan' => ['border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50', 'bg-blue-600 shadow-blue-600/30', 'bi-arrow-repeat', 'text-blue-700'],
        'approved' => ['border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50', 'bg-emerald-500 shadow-emerald-500/30', 'bi-patch-check-fill', 'text-emerald-700'],
    ];

    $teks = [
        'belum'    => ['Kamu belum mengajukan tempat PKL', 'Ajukan sekarang supaya bisa diproses oleh wali kelas, guru BK, kesiswaan, dan kepala jurusan.', 'Ajukan Sekarang'],
        'ditolak'  => ['Pengajuan PKL kamu ditolak', 'Silakan ajukan ulang dengan tempat PKL yang lain.', 'Ajukan Ulang'],
        'draft'    => ['Pengajuan PKL terkirim', 'Menunggu admin melengkapi guru pembimbing & jadwal sebelum masuk proses approval.', 'Lihat Status'],
        'diajukan' => ['Pengajuan PKL sedang diproses', 'Persetujuan berjalan bertahap. Klik untuk melihat siapa saja yang sudah menyetujui.', 'Lihat Status'],
        'approved' => ['Pengajuan PKL disetujui', 'Seluruh tahap approval sudah selesai. Klik untuk melihat detailnya.', 'Lihat Status'],
    ];
@endphp

@if ($info)
    @php
        [$kartu, $ikonBg, $ikon, $teksAksi] = $tema[$info['state']];
        [$judul, $deskripsi, $tombol] = $teks[$info['state']];
    @endphp

    <a href="{{ $info['url'] }}"
        class="flex items-center gap-4 rounded-2xl border {{ $kartu }} p-4 sm:p-5 shadow-sm hover:shadow-md transition-all group">
        <div class="w-11 h-11 rounded-xl {{ $ikonBg }} text-white flex items-center justify-center flex-shrink-0 shadow-md">
            <i class="bi {{ $ikon }} text-lg"></i>
        </div>

        <div class="flex-1 min-w-0">
            <div class="font-bold text-slate-900">{{ $judul }}</div>
            <div class="text-xs sm:text-sm text-slate-600">{{ $deskripsi }}</div>

            @if ($info['state'] === 'diajukan')
                <div class="mt-2 flex items-center gap-2">
                    <div class="h-1.5 w-32 rounded-full bg-white/80 overflow-hidden border border-blue-100">
                        <div class="h-full bg-blue-600" style="width: {{ ($info['progress'] / 4) * 100 }}%"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-blue-700">{{ $info['progress'] }} dari 4 tahap disetujui</span>
                </div>
            @endif

            @if ($info['tempat'] && $info['state'] !== 'belum')
                <div class="mt-1 text-[11px] text-slate-500">
                    <i class="bi bi-building"></i> {{ $info['tempat'] }}
                </div>
            @endif
        </div>

        <span class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold {{ $teksAksi }} group-hover:translate-x-0.5 transition-transform">
            {{ $tombol }} <i class="bi bi-arrow-right"></i>
        </span>
    </a>
@endif