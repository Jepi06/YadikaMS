{{-- Variabel: $percobaan, $quiz, $soal (id, pertanyaan, opsi), $tersimpan [soal_id => jawaban], $sisaDetik --}}
@extends('lms.layouts.app')

@section('title', $quiz->judul . ' - LMS Yadika')
@section('breadcrumb', 'Mengerjakan Quiz')

@section('content')
    {{-- Pita waktu (menempel di atas) --}}
    <div class="sticky top-[64px] z-20 -mx-1 px-4 py-3 rounded-xl bg-slate-900 text-white shadow-lg flex items-center justify-between gap-3">
        <div class="min-w-0">
            <div class="font-semibold truncate">{{ $quiz->judul }}</div>
            <div class="text-xs text-slate-300"><span id="jmlTerjawab">0</span> / {{ $soal->count() }} terjawab • <span id="infoSimpan" class="text-emerald-300">jawaban tersimpan otomatis</span></div>
        </div>
        <div class="text-right shrink-0">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Sisa waktu</div>
            <div id="timer" class="text-2xl font-mono font-bold">--:--</div>
        </div>
    </div>

    @if ($quiz->deskripsi)
        <div class="rounded-lg bg-blue-50 border border-blue-100 text-blue-800 text-sm px-4 py-3">{{ $quiz->deskripsi }}</div>
    @endif

    <form id="formQuiz" method="POST" action="{{ route('lms.siswa.quiz.selesai', $percobaan) }}" class="space-y-4">
        @csrf
        @foreach ($soal as $i => $s)
            <div class="p-5 rounded-xl bg-white border border-slate-200/70 shadow-sm space-y-3" data-soal="{{ $s['id'] }}">
                <p class="font-semibold text-slate-900 whitespace-pre-line"><span class="text-slate-400">{{ $i + 1 }}.</span> {{ $s['pertanyaan'] }}</p>
                <div class="space-y-2">
                    @foreach ($s['opsi'] as $huruf => $teks)
                        <label class="flex items-start gap-3 px-4 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400">
                            <input type="radio" name="j_{{ $s['id'] }}" value="{{ $huruf }}" class="mt-1 accent-blue-600 pilihan"
                                data-soal="{{ $s['id'] }}" @checked(($tersimpan[$s['id']] ?? null) === $huruf)>
                            <span class="font-mono text-slate-500">{{ $huruf }}.</span>
                            <span class="text-sm text-slate-800 whitespace-pre-line">{{ $teks }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" id="btnSelesai" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-sm">
                <i class="bi bi-check2-circle"></i> Selesai &amp; Kumpulkan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
(function () {
    const URL_JAWAB = @json(route('lms.siswa.quiz.jawab', $percobaan));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const TOTAL = {{ $soal->count() }};
    const form = document.getElementById('formQuiz');
    const info = document.getElementById('infoSimpan');
    let sisa = {{ $sisaDetik }};
    let kirimUlang = false;

    // ── hitung terjawab ──
    function hitung() {
        const n = new Set([...document.querySelectorAll('.pilihan:checked')].map(r => r.dataset.soal)).size;
        document.getElementById('jmlTerjawab').textContent = n;
    }

    // ── simpan otomatis tiap pilihan ──
    async function simpan(soalId, jawaban) {
        info.textContent = 'menyimpan…'; info.className = 'text-amber-300';
        try {
            const r = await fetch(URL_JAWAB, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF},
                body: JSON.stringify({soal_id: soalId, jawaban}),
            });
            if (!r.ok) throw new Error(r.status);
            info.textContent = 'jawaban tersimpan otomatis'; info.className = 'text-emerald-300';
        } catch (e) {
            info.textContent = 'gagal menyimpan — periksa koneksi'; info.className = 'text-rose-300';
        }
    }
    document.addEventListener('change', e => {
        if (e.target.classList?.contains('pilihan')) { simpan(e.target.dataset.soal, e.target.value); hitung(); }
    });

    // ── timer (berdasarkan sisa dari server) ──
    const mulaiLokal = Date.now();
    const timer = document.getElementById('timer');
    function tick() {
        const s = Math.max(0, sisa - Math.floor((Date.now() - mulaiLokal) / 1000));
        const m = Math.floor(s / 60), d = s % 60;
        timer.textContent = String(m).padStart(2, '0') + ':' + String(d).padStart(2, '0');
        timer.className = 'text-2xl font-mono font-bold ' + (s <= 60 ? 'text-rose-400' : '');
        if (s === 0) { form.submit(); return; }
        setTimeout(tick, 1000);
    }
    tick();

    // ── konfirmasi sebelum kumpul manual ──
    form.addEventListener('submit', e => {
        if (form.dataset.auto) return;
        const n = new Set([...document.querySelectorAll('.pilihan:checked')].map(r => r.dataset.soal)).size;
        const kosong = TOTAL - n;
        if (!confirm(kosong > 0 ? kosong + ' soal belum dijawab. Kumpulkan sekarang?' : 'Kumpulkan jawaban sekarang?')) e.preventDefault();
    });
    window.addEventListener('beforeunload', e => { if (!form.dataset.done) { e.preventDefault(); e.returnValue = ''; } });
    form.addEventListener('submit', () => { form.dataset.done = '1'; });

    hitung();
})();
</script>
@endpush
