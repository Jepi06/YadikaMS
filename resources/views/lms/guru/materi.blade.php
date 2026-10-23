{{-- resources/views/lms/guru/materi.blade.php --}}
{{--
    Dipanggil dari Guru\MateriController@index, route: lms.guru.materi.index
    Variabel: $pengampuMapel (with mataPelajaran, kelas) dan daftar materi
    ($materi atau $daftarMateri — salah satu saja cukup).
    Opsional: $materiGlobal → materi global milik kelas lain (read-only, dengan pengampuMapel.kelas).
--}}
<link href="/css/materi.css" rel="stylesheet">
@extends('lms.layouts.app')

@section('title', 'Materi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))
@section('breadcrumb', 'Kelola Materi')

@section('content')
    @php
        $semua = collect($materi ?? ($daftarMateri ?? []));

        // Materi milik kelas ini (bisa diedit) vs materi global dari kelas lain (read-only)
        $milikIni = fn($m) => ($m->pengampu_mapel_id ?? $pengampuMapel->id) == $pengampuMapel->id;
        $daftar = $semua->filter($milikIni)->values();
        $dariKelasLain = collect($materiGlobal ?? [])
            ->merge($semua->reject($milikIni))
            ->unique('id')
            ->values();

        // Nilai mode akses (value radio/select) => tampilan
        $modeInfo = [
            'bebas' => [
                'label' => 'Bebas — Langsung Terbuka',
                'badge' => 'bg-emerald-50 text-emerald-800',
                'dot' => 'bg-emerald-500',
            ],
            'berurutan' => [
                'label' => 'Berurutan — Syarat Materi Sebelumnya',
                'badge' => 'bg-blue-50 text-blue-800',
                'dot' => 'bg-blue-500',
            ],
            'manual' => [
                'label' => 'Dibuka Guru — Terkunci Manual',
                'badge' => 'bg-amber-50 text-amber-800',
                'dot' => 'bg-amber-500',
            ],
            'tanggal' => ['label' => 'Terjadwal', 'badge' => 'bg-purple-50 text-purple-800', 'dot' => 'bg-purple-600'],
        ];
        $modeOf = fn($m) => strtolower($m->mode_akses ?? 'bebas');
        $jadwalOf = fn($m) => $m->buka_pada;
        $isGlobal = fn($m) => (bool) ($m->is_global ?? false);

        $total = $daftar->count();
        $terjadwal = $daftar->filter(fn($m) => $modeOf($m) === 'tanggal')->count();
        $manual = $daftar->filter(fn($m) => $modeOf($m) === 'manual')->count();
        $lampiran = $daftar->filter(fn($m) => !empty($m->file_path))->count();
        $nGlobal = $daftar->filter(fn($m) => $isGlobal($m))->count();
    @endphp

    @php
        // Class CSS desain baru (terpisah dari $modeInfo di atas agar logika lama tidak tersentuh)
        $modeCls = [
            'bebas' => ['badge-bebas', 'dot-emerald'],
            'berurutan' => ['badge-berurutan', 'dot-blue'],
            'manual' => ['badge-manual', 'dot-amber'],
            'tanggal' => ['badge-tanggal', 'dot-purple'],
        ];
    @endphp

    

    <div class="mp">

        {{-- Header --}}
        <div class="page-header">
            <div class="header-left">
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('lms.guru.kelas.index') }}"><i class="bi bi-door-open"></i> Kelas Saya</a>
                    <i class="bi bi-chevron-right breadcrumb-sep"></i>
                    <span class="breadcrumb-current">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }}</span>
                    <i class="bi bi-chevron-right breadcrumb-sep"></i>
                    <span class="breadcrumb-active">Kelola Materi</span>
                </nav>
                <div class="title-wrapper">
                    <h1 class="page-title">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h1>
                    <span class="class-badge">
                        {{ $pengampuMapel->kelas->nama_kelas ?? '-' }}
                        @if ($pengampuMapel->tahun_ajaran ?? null)
                            · {{ $pengampuMapel->tahun_ajaran }}
                        @endif
                    </span>
                </div>
            </div>
            <a href="{{ route('lms.guru.kelas.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelas
            </a>
        </div>

        {{-- Ringkasan --}}
        <section class="stats-grid" aria-label="Ringkasan Materi">
            @foreach ([['Total Materi', $total, 'bi-journal-text', 'icon-blue'], ['Global (Kelas Lain)', $nGlobal, 'bi-globe2', 'icon-indigo'], ['Akses Terjadwal', $terjadwal, 'bi-clock', 'icon-purple'], ['Dibuka Manual', $manual, 'bi-lock', 'icon-amber'], ['Lampiran File', $lampiran, 'bi-paperclip', 'icon-slate']] as [$label, $nilai, $ikon, $warna])
                <div class="stat-card">
                    <div>
                        <p class="stat-label">{{ $label }}</p>
                        <p class="stat-value">{{ $nilai }}</p>
                    </div>
                    <div class="stat-icon {{ $warna }}">
                        <i class="bi {{ $ikon }}"></i>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Form tambah materi --}}
        <section>
            <details class="accordion-card" {{ $errors->any() ? 'open' : '' }}>
                <summary class="accordion-summary">
                    <div class="accordion-title-box">
                        <div class="accordion-icon"><i class="bi bi-file-earmark-plus"></i></div>
                        <div>
                            <h2 class="accordion-heading">Tambah Materi Pembelajaran Baru</h2>
                            <p class="accordion-sub">Unggah file, tautan video, atau instruksi untuk siswa</p>
                        </div>
                    </div>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </summary>

                <form method="POST" action="{{ route('lms.guru.materi.store', $pengampuMapel) }}"
                    enctype="multipart/form-data" class="form-content">
                    @csrf

                    @if ($errors->any())
                        <div class="form-errors">
                            <ul>
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-grid">
                        {{-- Kiri --}}
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">Judul Materi <span class="required">*</span></label>
                                <input type="text" name="judul" value="{{ old('judul') }}" required
                                    placeholder="Contoh: 05. RESTful API dengan Laravel Sanctum" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tautan Video / Referensi <span class="hint">(opsional)</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-link-45deg"></i>
                                    <input type="url" name="link_url" value="{{ old('link_url') }}"
                                        placeholder="https://youtu.be/... atau https://drive.google.com/..."
                                        class="form-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deskripsi &amp; Instruksi</label>
                                <textarea name="deskripsi" rows="4" placeholder="Poin capaian pembelajaran atau instruksi praktikum..."
                                    class="form-textarea">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>

                        {{-- Kanan --}}
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">
                                    <span>Berkas Lampiran</span>
                                    <span class="hint">Maks. 10 MB</span>
                                </label>
                                <div class="dropzone-container">
                                    <input type="file" name="file" id="inputFile"
                                        onchange="document.getElementById('namaFile').textContent = this.files[0]?.name ?? 'Klik untuk telusuri atau seret berkas ke sini'">
                                    <div class="dropzone-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                                    <p id="namaFile" class="dropzone-text">Klik untuk telusuri atau seret berkas ke sini</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Mode Hak Akses Siswa</label>
                                <div class="radio-segment">
                                    @foreach (['bebas' => 'Bebas', 'berurutan' => 'Berurutan', 'manual' => 'Dibuka Guru', 'tanggal' => 'Terjadwal'] as $val => $lbl)
                                        <label>
                                            <input type="radio" name="mode_akses" value="{{ $val }}"
                                                onchange="toggleJadwalBaru()"
                                                {{ old('mode_akses', 'bebas') === $val ? 'checked' : '' }}>
                                            <div class="segment-btn">
                                                <span class="segment-dot {{ $modeCls[$val][1] }}"></span>{{ $lbl }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div id="boxJadwalBaru" class="form-group {{ old('mode_akses') === 'tanggal' ? '' : 'hidden' }}">
                                <label class="form-label">Tanggal &amp; Waktu Terbuka</label>
                                <input type="datetime-local" name="buka_pada" value="{{ old('buka_pada') }}"
                                    class="form-input">
                            </div>

                            {{-- Toggle: khusus kelas ini / global --}}
                            <label class="toggle-card">
                                <div class="toggle-switch">
                                    <input type="checkbox" name="is_global" value="1"
                                        {{ old('is_global') ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </div>
                                <div class="toggle-text">
                                    <div class="toggle-title">
                                        <i class="bi bi-globe2"></i> Bagikan ke kelas lain (Global)
                                    </div>
                                    <p class="toggle-desc">
                                        Nonaktif: materi hanya untuk <strong>{{ $pengampuMapel->kelas->nama_kelas ?? 'kelas ini' }}</strong>.
                                        Aktif: siswa di kelas lain yang belajar mata pelajaran yang sama juga bisa membukanya.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save"></i> Simpan Materi
                        </button>
                    </div>
                </form>
            </details>
        </section>

        {{-- Daftar materi --}}
        <section>
            <div class="section-header">
                <div>
                    <h2 class="section-title">Daftar Materi Diterbitkan</h2>
                    <p class="section-desc">Kelola akses, tautan, lampiran, dan cakupan kelas tiap materi.</p>
                </div>
                <span class="badge-count">Semua ({{ $total }})</span>
            </div>

            <div class="materi-list">
                @forelse ($daftar as $i => $m)
                    @php
                        $mode = $modeOf($m);
                        $info = $modeInfo[$mode] ?? $modeInfo['bebas'];
                        $cls = $modeCls[$mode] ?? $modeCls['bebas'];
                        $jadwal = $jadwalOf($m);
                        $terbuka = (bool) $m->dibuka_manual;
                        $global = $isGlobal($m);
                    @endphp

                    <article class="materi-card">
                        <div class="materi-main">
                            <div class="materi-number">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="materi-body">
                                <div class="materi-tags">
                                    <h3 class="materi-title">{{ $m->judul }}</h3>
                                    <span class="status-badge {{ $cls[0] }}">
                                        <span class="segment-dot {{ $cls[1] }}"></span>
                                        @if ($mode === 'tanggal' && $jadwal)
                                            Terjadwal · {{ \Carbon\Carbon::parse($jadwal)->translatedFormat('d M Y, H:i') }}
                                        @else
                                            {{ $info['label'] }}
                                        @endif
                                    </span>
                                    @if ($global)
                                        <span class="status-badge badge-global"
                                            title="Siswa kelas lain dengan mata pelajaran yang sama bisa membuka materi ini">
                                            <i class="bi bi-globe2"></i> Global
                                        </span>
                                    @else
                                        <span class="status-badge badge-khusus">
                                            <i class="bi bi-door-closed"></i> Khusus {{ $pengampuMapel->kelas->nama_kelas ?? 'kelas ini' }}
                                        </span>
                                    @endif
                                </div>

                                @if ($m->deskripsi ?? null)
                                    <p class="materi-desc">{{ $m->deskripsi }}</p>
                                @endif

                                <div class="materi-attachments">
                                    @if ($m->file_path ?? null)
                                        <a href="{{ route('lms.file.materi', $m) }}" target="_blank" rel="noopener"
                                            class="btn-attachment-file">
                                            <i class="bi bi-download"></i><span>Buka / Unduh</span>
                                        </a>
                                    @endif
                                    @if ($m->link_url ?? null)
                                        <a href="{{ $m->link_url }}" target="_blank" rel="noopener" class="attachment-link">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                            <span>{{ \Illuminate\Support\Str::limit(preg_replace('#^https?://#', '', $m->link_url), 40) }}</span>
                                        </a>
                                    @endif
                                </div>

                                {{-- Ubah tautan (PUT lms.guru.materi.link) --}}
                                @if (Route::has('lms.guru.materi.link'))
                                    <details class="link-edit-details">
                                        <summary><i class="bi bi-pencil"></i> Ubah tautan</summary>
                                        <form method="POST" action="{{ route('lms.guru.materi.link', $m) }}"
                                            class="link-edit-form">
                                            @csrf @method('PUT')
                                            <input type="url" name="link_url" value="{{ $m->link_url }}"
                                                placeholder="https://..." class="link-edit-input">
                                            <button type="submit" class="btn-save-sm">Simpan</button>
                                        </form>
                                    </details>
                                @endif
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="materi-actions">
                            {{-- Toggle global / khusus kelas (POST lms.guru.materi.global) --}}
                            @if (Route::has('lms.guru.materi.global'))
                                <form method="POST" action="{{ route('lms.guru.materi.global', $m) }}">
                                    @csrf
                                    <button type="submit"
                                        title="{{ $global ? 'Klik untuk jadikan khusus kelas ini' : 'Klik untuk bagikan ke kelas lain' }}"
                                        class="btn-toggle-badge {{ $global ? 'active' : '' }}">
                                        <span class="mini-switch {{ $global ? 'active' : '' }}"><span class="mini-thumb"></span></span>
                                        <span>{{ $global ? 'Global' : 'Khusus Kelas' }}</span>
                                    </button>
                                </form>
                            @endif

                            @if ($mode === 'manual')
                                <form method="POST" action="{{ route('lms.guru.materi.toggle', $m) }}">
                                    @csrf
                                    <button type="submit" class="btn-lock {{ $terbuka ? 'unlocked' : 'locked' }}">
                                        <i class="bi {{ $terbuka ? 'bi-unlock' : 'bi-lock' }}"></i>
                                        <span>{{ $terbuka ? 'Kunci Lagi' : 'Buka Sekarang' }}</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Ubah mode akses (PUT lms.guru.materi.akses) --}}
                            <form method="POST" action="{{ route('lms.guru.materi.akses', $m) }}"
                                class="select-access-box">
                                @csrf @method('PUT')
                                <i class="bi bi-sliders"></i>
                                <select name="mode_akses" onchange="aksesBerubah(this)" class="select-access">
                                    @foreach (['bebas' => 'Bebas', 'berurutan' => 'Berurutan', 'manual' => 'Dibuka Guru', 'tanggal' => 'Terjadwal'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected($mode === $val)>{{ $lbl }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="buka_pada"
                                    value="{{ $jadwal ? \Carbon\Carbon::parse($jadwal)->format('Y-m-d\TH:i') : '' }}"
                                    class="jadwal-input input-datetime-compact {{ $mode === 'tanggal' ? '' : 'hidden' }}">
                                <button type="submit"
                                    class="btn-simpan-akses btn-save-sm {{ $mode === 'tanggal' ? '' : 'hidden' }}">
                                    Simpan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('lms.guru.materi.destroy', $m) }}"
                                onsubmit="return confirm('Hapus materi ini? File lampiran ikut terhapus.{{ $global ? ' Materi ini juga akan hilang dari kelas lain.' : '' }}')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus materi" class="btn-icon-del">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-placeholder">
                        Belum ada materi untuk kelas ini. Klik "Tambah Materi Pembelajaran Baru" di atas.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Materi global dari kelas lain (read-only) --}}
        @if ($dariKelasLain->isNotEmpty())
            <section>
                <div class="section-header">
                    <div>
                        <h2 class="section-title">
                            <i class="bi bi-globe2" style="color: var(--indigo);"></i> Materi Global dari Kelas Lain
                        </h2>
                        <p class="section-desc">Dibagikan oleh guru kelas lain pada mata pelajaran yang sama. Siswa
                            {{ $pengampuMapel->kelas->nama_kelas ?? 'kelas ini' }} juga bisa membukanya.</p>
                    </div>
                    <span class="badge-count badge-count-indigo">{{ $dariKelasLain->count() }} materi</span>
                </div>

                <div class="materi-list">
                    @foreach ($dariKelasLain as $g)
                        <article class="materi-card global-card">
                            <div class="materi-main">
                                <div class="materi-number is-indigo">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="materi-body">
                                    <div class="materi-tags">
                                        <h3 class="materi-title">{{ $g->judul }}</h3>
                                        <span class="status-badge badge-global">
                                            <i class="bi bi-share"></i> Dari {{ $g->pengampuMapel->kelas->nama_kelas ?? 'kelas lain' }}
                                        </span>
                                    </div>
                                    @if ($g->deskripsi ?? null)
                                        <p class="materi-desc">{{ $g->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="materi-actions">
                                @if ($g->file_path ?? null)
                                    <a href="{{ route('lms.file.materi', $g) }}" target="_blank" rel="noopener"
                                        class="btn-attachment-file">
                                        <i class="bi bi-download"></i><span>Buka / Unduh</span>
                                    </a>
                                @endif
                                @if ($g->link_url ?? null)
                                    <a href="{{ $g->link_url }}" target="_blank" rel="noopener" class="attachment-link">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        <span>{{ \Illuminate\Support\Str::limit(preg_replace('#^https?://#', '', $g->link_url), 30) }}</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        // Form tambah: tampilkan input jadwal hanya untuk mode "terjadwal"
        function toggleJadwalBaru() {
            const mode = document.querySelector('input[name="mode_akses"]:checked')?.value;
            document.getElementById('boxJadwalBaru').classList.toggle('hidden', mode !== 'tanggal');
        }

        // Daftar materi: ganti mode langsung tersimpan, kecuali "terjadwal" (perlu isi tanggal dulu)
        function aksesBerubah(select) {
            const form = select.closest('form');
            const terjadwal = select.value === 'tanggal';
            form.querySelector('.jadwal-input').classList.toggle('hidden', !terjadwal);
            form.querySelector('.btn-simpan-akses').classList.toggle('hidden', !terjadwal);
            if (!terjadwal) form.submit();
        }
    </script>
@endpush