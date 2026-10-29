{{-- Dashboard siswa: @include('lms.siswa._poin-pelanggaran', ['siswa' => $siswa]) --}}
@php
    $pp = \App\Services\Lms\PelanggaranService::ringkasan($siswa, 5);
    $tg = $pp['tingkat'];
@endphp
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl {{ $tg['bg'] }} {{ $tg['teks'] }} flex items-center justify-center shrink-0">
                <i class="bi bi-shield-check text-2xl"></i>
            </div>
            <div>
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Poin Kedisiplinan</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold {{ $tg['teks'] }}">{{ $pp['saldo'] }}</span>
                    <span class="text-xs text-slate-500">poin (awal {{ $pp['deposit'] }})</span>
                </div>
            </div>
        </div>
        <a href="{{ route('lms.siswa.laporan.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold">
            <i class="bi bi-file-earmark-pdf"></i><span>Laporan Kehadiran &amp; Poin (PDF)</span>
        </a>
    </div>

    <div>
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="{{ $tg['bar'] }} h-full rounded-full" style="width: {{ $pp['persen'] }}%"></div>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $tg['bg'] }} {{ $tg['teks'] }}">{{ $tg['label'] }}</span>
            <span class="text-xs text-rose-600 font-semibold">−{{ $pp['pelanggaran'] }} pelanggaran</span>
            <span class="text-xs text-emerald-600 font-semibold">+{{ $pp['prestasi'] }} prestasi</span>
        </div>
        <p class="text-xs text-slate-500 mt-1">{{ $tg['tindakan'] }}</p>
    </div>

    @if ($pp['terbaru']->isNotEmpty())
        <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl">
            @foreach ($pp['terbaru'] as $r)
                <div class="flex items-start justify-between gap-3 px-4 py-2.5 text-sm">
                    <div class="min-w-0">
                        <div class="text-slate-800">{{ $r->nama_pelanggaran }}</div>
                        <div class="text-xs text-slate-400">{{ $r->tanggal->format('d M Y') }}@if($r->catatan) • {{ $r->catatan }}@endif</div>
                    </div>
                    <span class="font-mono font-bold shrink-0 {{ $r->poin == 0 ? 'text-slate-400' : ($r->tipe === 'prestasi' ? 'text-emerald-600' : 'text-rose-600') }}">
                        {{ $r->poin == 0 ? '–' : ($r->tipe === 'prestasi' ? '+' : '−') . $r->poin }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-emerald-700 bg-emerald-50 rounded-xl px-4 py-3">Poinmu masih penuh. Pertahankan, dan raih prestasi untuk menambah poin! 👏</p>
    @endif
</section>
