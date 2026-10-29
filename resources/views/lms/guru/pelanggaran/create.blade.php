@extends('lms.layouts.app')

@section('title', 'Catat Poin Siswa - LMS Yadika')
@section('breadcrumb', 'Catat Poin Siswa')

@section('content')
    @php $tipe = old('tipe', $tipeAwal); @endphp

    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catat Poin Siswa</h1>
        <p class="text-sm text-slate-500 mt-1">Deposit awal tiap siswa 500 poin. Pelanggaran mengurangi poin, prestasi menambah poin. Centang beberapa siswa sekaligus.</p>
        <p class="text-xs text-blue-700 mt-1"><i class="bi bi-people"></i> Cakupan Anda: {{ $cakupan }}</p>
    </section>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-sm text-rose-800">
            <ul class="list-disc ml-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.guru.pelanggaran.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @csrf

        <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4 lg:col-span-1 h-fit">
            {{-- Tipe --}}
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                <label class="cursor-pointer text-center">
                    <input type="radio" name="tipe" value="pelanggaran" class="peer hidden tipe" @checked($tipe === 'pelanggaran')>
                    <span class="block py-2 rounded-lg text-sm font-semibold text-slate-500 peer-checked:bg-rose-600 peer-checked:text-white">− Pelanggaran</span>
                </label>
                <label class="cursor-pointer text-center">
                    <input type="radio" name="tipe" value="prestasi" class="peer hidden tipe" @checked($tipe === 'prestasi')>
                    <span class="block py-2 rounded-lg text-sm font-semibold text-slate-500 peer-checked:bg-emerald-600 peer-checked:text-white">+ Prestasi</span>
                </label>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Kelas</label>
                <select id="kelas" name="kelas_id" class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" data-prestasi="{{ ($kelasPrestasiIds === null || in_array($k->id, $kelasPrestasiIds)) ? 1 : 0 }}"
                            @selected(old('kelas_id', $kelasId) == $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pelanggaran --}}
            <div id="boxPelanggaran" class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Jenis pelanggaran</label>
                <select id="selPelanggaran" name="jenis_pelanggaran_id" class="jenis w-full px-3 py-2 rounded-lg bg-slate-100 text-sm">
                    <option value="" data-poin="0">— Catatan saja (tanpa poin) —</option>
                    @foreach ($pelanggaranPerKategori as $kategori => $items)
                        <optgroup label="{{ $kategori }}">
                            @foreach ($items as $j)
                                <option value="{{ $j->id }}" data-poin="{{ $j->poin }}" @selected(old('jenis_pelanggaran_id') == $j->id)>
                                    {{ \Illuminate\Support\Str::limit($j->nama, 70) }} (−{{ $j->poin }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            {{-- Prestasi --}}
            <div id="boxPrestasi" class="space-y-2">
                <label class="text-xs font-semibold text-slate-600">Jenis prestasi</label>
                <select id="selPrestasi" name="jenis_pelanggaran_id" class="jenis w-full px-3 py-2 rounded-lg bg-slate-100 text-sm">
                    <option value="" data-poin="0">— Lainnya (isi manual) —</option>
                    @foreach ($prestasiPerKategori as $kategori => $items)
                        <optgroup label="{{ $kategori }}">
                            @foreach ($items as $j)
                                <option value="{{ $j->id }}" data-poin="{{ $j->poin }}" @selected(old('jenis_pelanggaran_id') == $j->id)>
                                    {{ \Illuminate\Support\Str::limit($j->nama, 70) }} (+{{ $j->poin }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div id="manualPrestasi" class="grid grid-cols-3 gap-2">
                    <input type="text" name="nama_manual" value="{{ old('nama_manual') }}" maxlength="150" placeholder="Nama prestasi"
                        class="col-span-2 h-10 px-3 rounded-lg bg-slate-100 text-sm">
                    <input type="number" name="poin_manual" value="{{ old('poin_manual') }}" min="1" max="100" placeholder="Poin"
                        class="h-10 px-3 rounded-lg bg-slate-100 text-sm">
                </div>
            </div>

            <p id="infoPoin" class="text-xs text-slate-500"></p>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" max="{{ now()->toDateString() }}"
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Catatan <span class="font-normal text-slate-400">(wajib bila pelanggaran tanpa jenis)</span></label>
                <textarea name="catatan" rows="3" maxlength="1000" class="w-full px-3 py-2 rounded-lg bg-slate-100 text-sm"
                    placeholder="Mis. terlambat 15 menit / juara 1 lomba LKS...">{{ old('catatan') }}</textarea>
            </div>

            <button id="btnSimpan" type="submit" disabled
                class="w-full h-11 rounded-lg bg-blue-600 hover:bg-blue-500 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-semibold">
                Simpan untuk <span id="jmlDipilih">0</span> siswa
            </button>
        </section>

        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm lg:col-span-2 overflow-hidden">
            <div class="p-4 space-y-3 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="cari" type="text" placeholder="Cari nama / NIS (toleran salah ketik)..."
                            class="w-full h-10 pl-10 pr-4 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 shrink-0 cursor-pointer">
                        <input type="checkbox" id="pilihSemua" class="rounded"> Pilih semua yang tampil
                    </label>
                </div>
                <details class="text-sm">
                    <summary class="cursor-pointer text-blue-600 font-semibold">Tempel daftar nama (cocokkan otomatis)</summary>
                    <div class="mt-2 space-y-2">
                        <textarea id="tempel" rows="4" class="w-full px-3 py-2 rounded-lg bg-slate-100 text-sm"
                            placeholder="Satu nama per baris atau pisahkan koma. Nama mirip dicocokkan dengan siswa di kelas ini."></textarea>
                        <div class="flex items-center gap-3">
                            <button type="button" id="btnCocok" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-semibold">Cocokkan &amp; centang</button>
                            <span id="hasilCocok" class="text-xs text-slate-500"></span>
                        </div>
                    </div>
                </details>
            </div>
            <div id="daftarSiswa" class="divide-y divide-slate-100 max-h-[560px] overflow-y-auto">
                <div class="p-8 text-center text-slate-400 text-sm">Memuat siswa...</div>
            </div>
        </section>
    </form>
@endsection

@push('scripts')
<script>
(function () {
    const URL_SISWA = @json(route('lms.guru.pelanggaran.siswa', ['kelas' => '__ID__']));
    const OLD = new Set(@json(array_map('intval', (array) old('siswa_ids', []))));
    const $ = id => document.getElementById(id);
    let siswa = [], pertama = true;

    const tipeNow = () => document.querySelector('.tipe:checked').value;

    function aturTipe() {
        const p = tipeNow() === 'pelanggaran';
        $('boxPelanggaran').classList.toggle('hidden', !p);
        $('boxPrestasi').classList.toggle('hidden', p);
        $('selPelanggaran').disabled = !p;      // hanya satu select yang ikut terkirim
        $('selPrestasi').disabled = p;
        updatePoin();

        // prestasi hanya untuk kelas yang boleh (guru piket: kelas sendiri saja)
        const opts = [...$('kelas').options];
        opts.forEach(o => o.disabled = !p && o.dataset.prestasi === '0');
        if ($('kelas').selectedOptions[0].disabled) {
            const ok = opts.find(o => !o.disabled);
            if (ok) { $('kelas').value = ok.value; if (!pertama) muat(); }
        }
    }

    function updatePoin() {
        const p = tipeNow() === 'pelanggaran';
        const sel = p ? $('selPelanggaran') : $('selPrestasi');
        const poin = +sel.selectedOptions[0].dataset.poin;
        $('manualPrestasi').classList.toggle('hidden', p || sel.value !== '');
        $('infoPoin').textContent = p
            ? (poin ? `Setiap siswa terpilih berkurang ${poin} poin.` : 'Hanya dicatat, tanpa mengurangi poin.')
            : (sel.value ? `Setiap siswa terpilih bertambah ${poin} poin.` : 'Isi nama prestasi dan poin (maks. 100).');
    }

    const norm = s => (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim();
    function lev(a, b) {
        const m = a.length, n = b.length; if (!m) return n; if (!n) return m;
        let p = Array.from({length: n + 1}, (_, j) => j);
        for (let i = 1; i <= m; i++) { let c = [i];
            for (let j = 1; j <= n; j++) c[j] = Math.min(p[j] + 1, c[j - 1] + 1, p[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1));
            p = c; }
        return p[n];
    }
    const tokenCocok = (t, words) => words.some(w => w.includes(t) || lev(t, w) <= (t.length >= 6 ? 2 : (t.length >= 4 ? 1 : 0)));
    function skor(query, s) {
        const toks = norm(query).split(' ').filter(Boolean);
        if (!toks.length) return 1;
        const words = norm(s.nama).split(' ').concat([String(s.nis)]);
        return toks.filter(t => tokenCocok(t, words)).length / toks.length;
    }

    function render() {
        const box = $('daftarSiswa');
        if (!siswa.length) { box.innerHTML = '<div class="p-8 text-center text-slate-400 text-sm">Tidak ada siswa aktif di kelas ini.</div>'; return; }
        box.innerHTML = siswa.map(s => `
            <label class="row flex items-center gap-3 px-5 py-3 hover:bg-slate-50 cursor-pointer" data-id="${s.id}">
                <input type="checkbox" name="siswa_ids[]" value="${s.id}" class="cb rounded" ${OLD.has(s.id) ? 'checked' : ''}>
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-slate-900 truncate">${s.nama.replace(/</g, '&lt;')}</div>
                    <div class="text-xs text-slate-400 font-mono">${s.nis}</div>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full ${s.saldo < 500 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700'}">${s.saldo} poin</span>
            </label>`).join('');
        hitung();
    }
    function hitung() {
        const n = document.querySelectorAll('.cb:checked').length;
        $('jmlDipilih').textContent = n; $('btnSimpan').disabled = n === 0;
    }
    function filter() {
        const q = $('cari').value;
        document.querySelectorAll('#daftarSiswa .row').forEach(r => {
            r.classList.toggle('hidden', skor(q, siswa.find(x => x.id == r.dataset.id)) < 1);
        });
        $('pilihSemua').checked = false;
    }
    async function muat() {
        $('daftarSiswa').innerHTML = '<div class="p-8 text-center text-slate-400 text-sm">Memuat siswa...</div>';
        const r = await fetch(URL_SISWA.replace('__ID__', $('kelas').value), {headers: {'Accept': 'application/json'}});
        siswa = (await r.json()).siswa;
        if (!pertama) OLD.clear(); pertama = false; render();
    }

    document.addEventListener('change', e => { if (e.target.classList?.contains('cb')) hitung(); });
    document.querySelectorAll('.tipe').forEach(r => r.addEventListener('change', aturTipe));
    document.querySelectorAll('.jenis').forEach(s => s.addEventListener('change', updatePoin));
    $('kelas').addEventListener('change', muat);
    $('cari').addEventListener('input', filter);
    $('pilihSemua').addEventListener('change', e => {
        document.querySelectorAll('#daftarSiswa .row:not(.hidden) .cb').forEach(c => c.checked = e.target.checked);
        hitung();
    });
    $('btnCocok').addEventListener('click', () => {
        const baris = $('tempel').value.split(/[\n,;]+/).map(x => x.trim()).filter(Boolean);
        const tidak = []; let ok = 0;
        baris.forEach(nama => {
            let best = null, bs = 0;
            siswa.forEach(s => { const sc = skor(nama, s); if (sc > bs) { bs = sc; best = s; } });
            if (best && bs >= 0.5) { const cb = document.querySelector(`.row[data-id="${best.id}"] .cb`); if (cb) { cb.checked = true; ok++; } }
            else tidak.push(nama);
        });
        hitung();
        $('hasilCocok').textContent = `${ok} dicentang` + (tidak.length ? `, tidak ditemukan: ${tidak.join(', ')}` : '');
    });

    aturTipe(); muat();
})();
</script>
@endpush
