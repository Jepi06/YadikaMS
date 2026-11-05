<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Siswa - LMS SMK Yadika Soreang</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons Font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link href="/css/nilaiGuruLms.css" rel="stylesheet">

    <style>
        /* Tambahan untuk fitur Nilai Praktik, mode Sikap & Sikap custom */
        .mode-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .mode-box { border: 1px solid var(--slate-200, #e2e8f0); border-radius: 12px; padding: 12px 14px; background: var(--slate-50, #f8fafc); }
        .mode-box-title { font-size: .8rem; font-weight: 700; color: var(--slate-900, #0f172a); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .mode-option { display: flex; align-items: flex-start; gap: 8px; font-size: .82rem; color: var(--slate-700, #334155); padding: 5px 0; cursor: pointer; }
        .mode-option input { margin-top: 3px; }
        .mode-option small { display: block; color: var(--slate-400, #94a3b8); font-size: .72rem; }
        .mode-extra { margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--slate-200, #e2e8f0); font-size: .8rem; }
        .mode-extra input { width: 80px; margin: 0 6px; }
        .btn-praktik { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; font-weight: 600; font-size: .85rem; text-decoration: none; background: #fff; color: var(--ocean-blue, #0d47a1); border: 1px solid var(--ocean-blue, #0d47a1); }
        .btn-praktik:hover { background: var(--ocean-blue, #0d47a1); color: #fff; }
        .sikap-cell { display: flex; align-items: center; gap: 8px; }
        .custom-check { display: inline-flex; align-items: center; gap: 4px; font-size: .75rem; font-weight: 600; color: var(--slate-700, #334155); white-space: nowrap; cursor: pointer; }
        .js-sikap-select.is-dimmed { opacity: .45; }
        .js-sikap-nilai { margin-top: 6px; }
        .sub-gabungan { color: var(--ocean-blue, #0d47a1); font-weight: 600; }
        .bonus-note { font-size: .7rem; color: var(--slate-400, #94a3b8); margin-top: 3px; }
        [hidden] { display: none !important; }
    </style>
</head>
<body>
    @php
        $gabung = (bool) ($pengaturan->praktik_gabung_tugas ?? true);
        $sikapTambahan = ($pengaturan->sikap_mode ?? 'utama') === 'tambahan';
        $praktikMode = old('praktik_mode', $gabung ? 'gabung' : 'terpisah');
        $sikapMode = old('sikap_mode', $sikapTambahan ? 'tambahan' : 'utama');
    @endphp

    <!-- TOP NAVIGATION BAR -->
    <header class="site-header">
        <div class="container">
            <div class="navbar">
                <div class="brand-group">
                    <div class="brand-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="brand-text">
                        <div class="name">LMS Yadika</div>
                        <div class="sub">Portal Guru & Nilai Akademik</div>
                    </div>
                </div>

                <div class="teacher-profile-chip">
                    <div class="teacher-avatar">G</div>
                    <div class="teacher-meta">
                        <span class="teacher-name">Guru Pengampu</span>
                        <span class="teacher-role">SMK Yadika Soreang</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO HEADER: MATA PELAJARAN & KELAS -->
    <section class="page-banner">
        <div class="container">
            <div class="banner-flex">
                <div class="mapel-info-group">
                    <div class="mapel-icon-large">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <h1 class="banner-title">{{ $pengampuMapel->mataPelajaran->nama ?? 'Pemrograman Web & Perangkat Bergerak' }}</h1>
                        <div class="banner-breadcrumbs">
                            <span class="class-pill">{{ $pengampuMapel->kelas->nama_kelas ?? 'XII PPLG 1' }}</span>
                            <span>•</span>
                            <span>Rekapitulasi Nilai Akademik Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="banner-actions">
                    <a href="{{ route('lms.guru.dashboard') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Dasbor</span>
                    </a>
                    <a href="{{ route('lms.guru.nilai.praktik.index', $pengampuMapel) }}" class="btn-praktik">
                        <i class="bi bi-tools"></i>
                        <span>Nilai Praktik ({{ $jumlahPraktik }})</span>
                    </a>
                    <a href="{{ route('lms.guru.nilai.export', $pengampuMapel) }}" class="btn-export-excel">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        <span>Export ke Excel</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN BODY SECTION -->
    <main class="main-content">
        <div class="container">

            <!-- Flash Session Alerts -->
            @if (session('status'))
                <div class="alert-status-success">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.15rem;"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-status-error">
                    <i class="bi bi-exclamation-circle-fill" style="font-size: 1.15rem;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- CARD 1: PENGATURAN & BOBOT PENILAIAN -->
            <div class="card-bobot">
                <div class="card-header-styled">
                    <div class="header-title-flex">
                        <div class="header-icon-pill">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <span>Konfigurasi Bobot Penilaian</span>
                    </div>
                    <span class="header-hint">Digunakan dalam kalkulasi otomatis Nilai Akhir</span>
                </div>

                <div class="card-body-styled">
                    <form method="POST" action="{{ route('lms.guru.nilai.bobot', $pengampuMapel) }}" id="formBobot">
                        @csrf

                        <!-- Pilihan mode: Praktik & Sikap -->
                        <div class="mode-row">
                            <div class="mode-box">
                                <div class="mode-box-title"><i class="bi bi-tools"></i> Nilai Praktik</div>
                                <label class="mode-option">
                                    <input type="radio" name="praktik_mode" value="gabung" @checked($praktikMode === 'gabung')>
                                    <span>Gabung ke Nilai Tugas
                                        <small>Persentase praktik ikut persentase Tugas (rata-rata = total nilai tugas+praktik ÷ jumlah item).</small>
                                    </span>
                                </label>
                                <label class="mode-option">
                                    <input type="radio" name="praktik_mode" value="terpisah" @checked($praktikMode === 'terpisah')>
                                    <span>Persentase sendiri
                                        <small>Jadi 5 komponen: Tugas, Praktik, STS, SAS, Sikap.</small>
                                    </span>
                                </label>
                            </div>

                            <div class="mode-box">
                                <div class="mode-box-title"><i class="bi bi-emoji-smile"></i> Sikap &amp; Keaktifan</div>
                                <label class="mode-option">
                                    <input type="radio" name="sikap_mode" value="utama" @checked($sikapMode === 'utama')>
                                    <span>Bagian dari total 100%
                                        <small>Sikap punya bobot persen seperti komponen lain.</small>
                                    </span>
                                </label>
                                <label class="mode-option">
                                    <input type="radio" name="sikap_mode" value="tambahan" @checked($sikapMode === 'tambahan')>
                                    <span>Nilai tambahan (bonus)
                                        <small>Komponen lain = 100%, sikap menambah poin. Nilai akhir maksimal tetap 100.</small>
                                    </span>
                                </label>
                                <div class="mode-extra" data-sikap="tambahan">
                                    Tambahan maksimal
                                    <input type="number" name="sikap_maks_tambahan" id="inputMaksTambahan"
                                           class="field-input-number" min="1" max="100"
                                           value="{{ old('sikap_maks_tambahan', $pengaturan->sikap_maks_tambahan ?? 10) }}">
                                    poin
                                    <small style="display:block; color: var(--slate-400, #94a3b8); margin-top:4px;">
                                        Contoh: 10 → sikap bernilai 100 menambah 10 poin, bernilai 80 menambah 8 poin.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="bobot-grid">
                            <!-- Input Bobot Tugas -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span id="labelTugas">Tugas Harian</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number"
                                       name="bobot_tugas"
                                       class="field-input-number bobot-input"
                                       value="{{ old('bobot_tugas', $bobot->bobot_tugas ?? 30) }}"
                                       min="0"
                                       max="100"
                                       required>
                            </div>

                            <!-- Input Bobot Praktik (hanya mode terpisah) -->
                            <div class="input-control-box" data-praktik="terpisah">
                                <label class="label-custom">
                                    <span>Nilai Praktik</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number"
                                       name="bobot_praktik"
                                       class="field-input-number bobot-input"
                                       value="{{ old('bobot_praktik', $pengaturan->bobot_praktik ?? 0) }}"
                                       min="0"
                                       max="100">
                            </div>

                            <!-- Input Bobot STS -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>STS (Tengah Sem.)</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number"
                                       name="bobot_sts"
                                       class="field-input-number bobot-input"
                                       value="{{ old('bobot_sts', $bobot->bobot_sts ?? 25) }}"
                                       min="0"
                                       max="100"
                                       required>
                            </div>

                            <!-- Input Bobot SAS -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>SAS (Akhir Sem.)</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number"
                                       name="bobot_sas"
                                       class="field-input-number bobot-input"
                                       value="{{ old('bobot_sas', $bobot->bobot_sas ?? 30) }}"
                                       min="0"
                                       max="100"
                                       required>
                            </div>

                            <!-- Input Bobot Sikap (hanya mode utama) -->
                            <div class="input-control-box" data-sikap="utama">
                                <label class="label-custom">
                                    <span>Sikap & Keaktifan</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number"
                                       name="bobot_sikap"
                                       class="field-input-number bobot-input"
                                       value="{{ old('bobot_sikap', $bobot->bobot_sikap ?? 15) }}"
                                       min="0"
                                       max="100">
                            </div>

                            <!-- Indikator Total Bobot -->
                            <div class="input-control-box">
                                <div class="total-counter-box">
                                    <span class="total-counter-title">Total Bobot</span>
                                    <div id="totalBobot" class="total-display-value total-valid">100%</div>
                                </div>
                            </div>

                            <!-- Tombol Simpan Bobot -->
                            <div class="input-control-box">
                                <button type="submit" class="btn-save-dark" id="btnSimpanBobot">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>Simpan Bobot</span>
                                </button>
                            </div>
                        </div>

                        <div class="rule-note">
                            <i class="bi bi-info-circle-fill" style="color: var(--ocean-blue);"></i>
                            <span id="ruleNote">Ketentuan: Akumulasi total persentase bobot komponen aktif harus <strong>persis 100%</strong>.</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CARD 2: TABEL REKAP NILAI SISWA -->
            <form method="POST" action="{{ route('lms.guru.nilai.sikap', $pengampuMapel) }}">
                @csrf
                <div class="card-table-wrap">
                    <div class="card-header-styled">
                        <div class="header-title-flex">
                            <div class="header-icon-pill">
                                <i class="bi bi-table"></i>
                            </div>
                            <span>Rekap Nilai Siswa</span>
                        </div>
                        <span class="header-hint">
                            <i class="bi bi-lightning-charge-fill" style="color: #f59e0b;"></i>
                            Nilai Akhir dihitung otomatis berdasarkan persentase bobot
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">Siswa</th>
                                    <th style="width: 120px;">Rata-rata Tugas</th>
                                    <th style="width: 120px;">Rata-rata Praktik</th>
                                    <th style="width: 110px;">STS</th>
                                    <th style="width: 110px;">SAS</th>
                                    <th style="min-width: 250px;">Predikat Sikap</th>
                                    <th style="min-width: 200px;">Catatan Perkembangan Sikap</th>
                                    <th style="width: 120px; text-align: center;">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapSiswa as $siswaId => $r)
                                    @php
                                        $isCustom = (bool) old("sikap.$siswaId.custom", $r->sikap_custom);
                                        $angkaCustom = old("sikap.$siswaId.nilai_custom", $r->sikap_custom ? $r->sikap_nilai : '');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="student-cell">
                                                <div class="student-avatar">
                                                    {{ strtoupper(substr($r->siswa->nama ?? 'S', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="student-name-text">{{ $r->siswa->nama }}</span>
                                                    @unless ($r->lengkap)
                                                        <span class="badge-warning-custom">Belum Lengkap</span>
                                                    @endunless
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($r->rata_tugas !== null)
                                                <div class="badge-tugas">
                                                    <span>{{ number_format($r->rata_tugas, 1) }}</span>
                                                </div>
                                                <div class="tugas-count-sub">{{ $r->jumlah_tugas_dinilai }} tugas dinilai</div>
                                            @else
                                                <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                            @endif
                                            @if ($gabung && $r->rata_gabungan !== null)
                                                <div class="tugas-count-sub sub-gabungan">Gabungan: {{ number_format($r->rata_gabungan, 1) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($r->rata_praktik !== null)
                                                <div class="badge-tugas">
                                                    <span>{{ number_format($r->rata_praktik, 1) }}</span>
                                                </div>
                                                <div class="tugas-count-sub">{{ $r->jumlah_praktik_dinilai }} dari {{ $jumlahPraktik }} praktik</div>
                                            @else
                                                <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="ujian[{{ $siswaId }}][sts]"
                                                   class="table-input-number"
                                                   value="{{ $r->nilai_sts }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.1"
                                                   placeholder="0.0">
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="ujian[{{ $siswaId }}][sas]"
                                                   class="table-input-number"
                                                   value="{{ $r->nilai_sas }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.1"
                                                   placeholder="0.0">
                                        </td>
                                        <td>
                                            <div class="sikap-cell">
                                                <select name="sikap[{{ $siswaId }}][predikat]" class="table-select-custom js-sikap-select {{ $isCustom ? 'is-dimmed' : '' }}">
                                                    @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                                        <option value="{{ $predikat }}" @selected(old("sikap.$siswaId.predikat", $r->sikap_predikat ?? 'Baik') === $predikat)>
                                                            {{ $predikat }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label class="custom-check" title="Centang untuk mengisi nilai sikap secara manual (angka)">
                                                    <input type="checkbox"
                                                           class="js-sikap-custom"
                                                           name="sikap[{{ $siswaId }}][custom]"
                                                           value="1"
                                                           @checked($isCustom)>
                                                    Custom
                                                </label>
                                            </div>
                                            <input type="number"
                                                   name="sikap[{{ $siswaId }}][nilai_custom]"
                                                   class="table-input-number js-sikap-nilai"
                                                   value="{{ $angkaCustom }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.1"
                                                   placeholder="Nilai 0–100"
                                                   @unless ($isCustom) hidden @endunless>
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="sikap[{{ $siswaId }}][catatan]"
                                                   value="{{ $r->sikap_catatan }}"
                                                   class="table-input-text"
                                                   placeholder="Catatan guru (opsional)...">
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="score-pill-final {{ ($r->nilai_akhir ?? 0) >= 85 ? 'score-high' : 'score-mid' }}">
                                                {{ $r->nilai_akhir ?? '-' }}
                                            </span>
                                            @if ($sikapTambahan)
                                                <div class="bonus-note">termasuk +{{ number_format($r->bonus_sikap, 1) }} poin sikap</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="empty-state-row">
                                            <i class="bi bi-people" style="font-size: 2rem; display: block; margin-bottom: 8px; color: var(--slate-300);"></i>
                                            Belum ada data siswa di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($rekapSiswa->count())
                        <div class="table-action-footer">
                            <div class="footer-left-action">
                                <button type="submit" class="btn-submit-primary">
                                    <i class="bi bi-save2-fill"></i>
                                    <span>Simpan STS, SAS &amp; Sikap</span>
                                </button>
                                <div class="footer-update-note">
                                    <i class="bi bi-shield-check" style="color: var(--emerald-text);"></i>
                                    <span>Nilai Akhir langsung ter-update otomatis begitu disimpan.</span>
                                </div>
                            </div>
                            <div style="font-size: 0.775rem; color: var(--slate-400);">
                                Total {{ $rekapSiswa->count() }} siswa terdaftar
                            </div>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-flex">
                <div class="footer-brand">
                    <div class="footer-badge-icon">Y</div>
                    <strong style="color: var(--slate-900);">SMK Yadika Soreang</strong>
                    <span>|</span>
                    <span>Learning Management System — Modul Guru Pengampu</span>
                </div>
                <div>
                    &copy; 2025 SMK Yadika Soreang. Hak Cipta Dilindungi.
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const form = document.getElementById('formBobot');
            const displayEl = document.getElementById('totalBobot');
            const btnSubmit = document.getElementById('btnSimpanBobot');
            const labelTugas = document.getElementById('labelTugas');
            const ruleNote = document.getElementById('ruleNote');

            function modeAktif(name) {
                const el = form.querySelector('input[name="' + name + '"]:checked');
                return el ? el.value : null;
            }

            // Tampilkan/sembunyikan kolom bobot sesuai mode. Input yang disembunyikan di-disable
            // supaya tidak ikut dihitung & tidak ikut terkirim.
            function sinkronMode() {
                const praktik = modeAktif('praktik_mode');
                const sikap = modeAktif('sikap_mode');

                form.querySelectorAll('[data-praktik]').forEach(function (el) {
                    const tampil = el.dataset.praktik === praktik;
                    el.hidden = !tampil;
                    el.querySelectorAll('input').forEach(function (i) { i.disabled = !tampil; });
                });
                form.querySelectorAll('[data-sikap]').forEach(function (el) {
                    const tampil = el.dataset.sikap === sikap;
                    el.hidden = !tampil;
                    el.querySelectorAll('input').forEach(function (i) { i.disabled = !tampil; });
                });

                labelTugas.textContent = praktik === 'gabung' ? 'Tugas + Praktik' : 'Tugas Harian';

                let note = 'Ketentuan: total persentase komponen aktif harus <strong>persis 100%</strong>';
                note += sikap === 'tambahan'
                    ? ' (Sikap tidak dihitung di sini, karena menjadi poin tambahan).'
                    : '.';
                ruleNote.innerHTML = note;

                hitungTotalBobot();
            }

            function hitungTotalBobot() {
                let total = 0;
                form.querySelectorAll('.bobot-input').forEach(function (el) {
                    if (!el.disabled) total += Number(el.value || 0);
                });

                displayEl.textContent = total + '%';
                const valid = total === 100;
                displayEl.className = 'total-display-value ' + (valid ? 'total-valid' : 'total-invalid');
                btnSubmit.disabled = !valid;
                btnSubmit.style.opacity = valid ? '1' : '0.6';
            }

            form.querySelectorAll('.bobot-input').forEach(function (el) {
                el.addEventListener('input', hitungTotalBobot);
            });
            form.querySelectorAll('input[type="radio"]').forEach(function (el) {
                el.addEventListener('change', sinkronMode);
            });

            sinkronMode();

            // Sikap custom: centang -> tampilkan input angka, dropdown diredupkan
            document.querySelectorAll('.js-sikap-custom').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    const cell = cb.closest('td');
                    const select = cell.querySelector('.js-sikap-select');
                    const angka = cell.querySelector('.js-sikap-nilai');
                    select.classList.toggle('is-dimmed', cb.checked);
                    angka.hidden = !cb.checked;
                    if (cb.checked) angka.focus();
                });
            });
        })();
    </script>
</body>
</html>
