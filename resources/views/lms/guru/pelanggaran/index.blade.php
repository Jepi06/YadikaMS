@extends('lms.layouts.app')

@section('title', 'Poin Siswa - LMS Yadika')
@section('breadcrumb', 'Poin Siswa')

@section('content')
    @php
        use App\Services\Lms\PelanggaranAkses;
        use App\Services\Lms\PelanggaranService;
    @endphp

    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Poin Siswa</h1>
            <p class="text-sm text-slate-500 mt-1">Setiap siswa memiliki deposit 500 poin. Pelanggaran mengurangi poin, prestasi menambah poin.</p>
            <p class="text-xs text-blue-700 mt-1"><i class="bi bi-people"></i> Cakupan Anda: {{ $cakupan }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('lms.guru.pelanggaran.create', ['kelas_id' => $kelasId, 'tipe' => 'pelanggaran']) }}"
                style="background-color:#e11d48;color:#fff"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold shadow-sm hover:opacity-90">
                <i class="bi bi-dash-circle"></i><span>Catat Pelanggaran</span>
            </a>
            <a href="{{ route('lms.guru.pelanggaran.create', ['kelas_id' => $kelasId, 'tipe' => 'prestasi']) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-sm">
                <i class="bi bi-trophy"></i><span>Catat Prestasi</span>
            </a>
        </div>
    </section>

    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-sm text-emerald-800 flex gap-2">
            <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('naik_tingkat'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900">
            <div class="font-semibold mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Siswa berikut naik tingkat sanksi, mohon ditindaklanjuti:</div>
            <ul class="list-disc ml-5">
                @foreach (session('naik_tingkat') as $n)<li>{{ $n }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <select name="tipe" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                <option value="">Pelanggaran &amp; prestasi</option>
                <option value="pelanggaran" @selected(request('tipe') === 'pelanggaran')>Pelanggaran saja</option>
                <option value="prestasi" @selected(request('tipe') === 'prestasi')>Prestasi saja</option>
            </select>
            <select name="kelas_id" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                <option value="">Semua kelas cakupan saya</option>
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
            <select name="kategori" onchange="this.form.submit()" class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                <option value="">Semua kategori</option>
                @foreach ($kategoriList as $kt)
                    <option value="{{ $kt }}" @selected($kategori === $kt)>{{ $kt }}</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / NIS siswa..."
                class="h-10 px-3 rounded-lg bg-slate-100 text-sm md:col-span-1">
            <button class="h-10 rounded-lg bg-slate-800 text-white text-sm font-semibold">Cari</button>
        </form>
    </section>

    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-5">Tanggal</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Poin</th>
                        <th class="py-3 px-4 text-center">Saldo siswa</th>
                        <th class="py-3 px-4">Dicatat oleh</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $r)
                        @php $saldo = $saldoPerSiswa[$r->siswa_id] ?? 500; $tg = PelanggaranService::tingkat(500 - $saldo); $prestasi = $r->tipe === 'prestasi'; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-5 whitespace-nowrap text-slate-600">{{ $r->tanggal->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $r->siswa->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $r->siswa->kelas->nama_kelas ?? '-' }} • {{ $r->siswa->nis ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 max-w-md">
                                <div class="text-slate-800">{{ $r->nama_pelanggaran }}</div>
                                <div class="text-xs text-slate-400">{{ $r->kategori }}</div>
                                @if ($r->catatan)<div class="text-xs text-slate-500 mt-1 italic">“{{ $r->catatan }}”</div>@endif
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold {{ $r->poin == 0 ? 'text-slate-400' : ($prestasi ? 'text-emerald-600' : 'text-rose-600') }}">
                                {{ $r->poin == 0 ? '–' : ($prestasi ? '+' : '−') . $r->poin }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $tg['bg'] }} {{ $tg['teks'] }}" title="{{ $tg['label'] }}">{{ $saldo }} poin</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $r->pencatat->name ?? '-' }}<br>
                                <span class="text-slate-400">{{ PelanggaranAkses::labelPeran($r->peran_pencatat) }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if ($r->bisa_hapus)
                                    <form method="POST" action="{{ route('lms.guru.pelanggaran.destroy', $r) }}"
                                        onsubmit="return confirm('Hapus catatan ini? Saldo poin siswa akan dikoreksi.')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 hover:text-rose-800 text-xs font-semibold"><i class="bi bi-trash"></i> Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="bi bi-emoji-smile text-3xl text-slate-300 block mb-2"></i>Belum ada catatan poin.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50">{{ $riwayat->links() }}</div>
    </section>
@endsection
