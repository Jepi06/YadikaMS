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

    {{-- Gaya tambahan untuk opsi penilaian (tidak bergantung pada nilaiGuruLms.css) --}}
    <style>
        .opsi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .opsi-box { border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px 16px; background: #fff; }
        .opsi-box h4 { margin: 0 0 8px; font-size: .9rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .opsi-radio { display: flex; gap: 10px; align-items: flex-start; padding: 8px 10px; border-radius: 10px; cursor: pointer; border: 1px solid transparent; }
        .opsi-radio:hover { background: #f8fafc; }
        .opsi-radio input { margin-top: 4px; accent-color: #1d4ed8; }
        .opsi-radio strong { display: block; font-size: .85rem; color: #0f172a; }
        .opsi-radio small { display: block; font-size: .75rem; color: #64748b; line-height: 1.4; margin-top: 2px; }
        .opsi-radio:has(input:checked) { background: #eff6ff; border-color: #bfdbfe; }
        .opsi-bonus { border: 1px dashed #93c5fd; background: #f0f7ff; border-radius: 14px; padding: 14px 16px; margin-top: 16px; }
        .opsi-bonus-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin: 10px 0; }
        .opsi-preview { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
        .opsi-preview span { font-size: .75rem; background: #fff; border: 1px solid #dbeafe; border-radius: 999px; padding: 3px 10px; color: #1e3a8a; }
        .opsi-warn { margin-top: 12px; font-size: .8rem; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 8px 12px; }
        .is-hidden { display: none !important; }
        .bonus-pill { display: inline-block; font-family: monospace; font-weight: 700; font-size: .85rem; color: #047857; background: #ecfdf5; border-radius: 8px; padding: 3px 9px; }
        .btn-praktik { display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border-radius: 10px; background: #fff; color: #1e293b; border: 1px solid #cbd5e1; font-weight: 600; font-size: .85rem; text-decoration: none; }
        .btn-praktik:hover { background: #f1f5f9; }
    </style>
</head>
<body>
@php
    $terpisah = $bw['praktik_terpisah'];
    $include  = $bw['sikap_include'];
    $quizMode  = $bw['quiz_mode'];
    $quizAktif = $quizMode !== 'nonaktif';
    $quizTam   = $quizMode === 'tambahan';
    $jumlahKolom = 7 + ($terpisah ? 1 : 0) + ($include ? 0 : 1) + ($quizAktif ? 1 : 0) + ($quizTam ? 1 : 0);
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
                    <a href="{{ route('lms.guru.kelas.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Kelas</span>
                    </a>
                    {{-- FIX #1: nama route sesuai web.php --}}
                    <a href="{{ route('lms.guru.nilai.praktik.index', $pengampuMapel) }}" class="btn-praktik">
                        <i class="bi bi-tools"></i>
                        <span>Kelola Praktik</span>
                    </a>
                    <a href="{{ route('lms.guru.quiz.index', $pengampuMapel) }}" class="btn-praktik">
                        <i class="bi bi-patch-question"></i>
                        <span>Kelola Quiz</span>
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

            <!-- CARD 1: BOBOT PENILAIAN -->
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

                        {{-- PILIHAN CARA PENILAIAN --}}
                        <div class="opsi-grid">
                            <div class="opsi-box">
                                <h4><i class="bi bi-tools" style="color: var(--ocean-blue, #1d4ed8);"></i> Nilai Praktik</h4>
                                <label class="opsi-radio">
                                    <input type="radio" name="praktik_mode" value="gabung" @checked(old('praktik_mode', $terpisah ? 'terpisah' : 'gabung') === 'gabung')>
                                    <span>
                                        <strong>Digabung dengan nilai tugas</strong>
                                        <small>Semua tugas dan praktik dirata-rata bersama dalam satu komponen "Tugas".</small>
                                    </span>
                                </label>
                                <label class="opsi-radio">
                                    <input type="radio" name="praktik_mode" value="terpisah" @checked(old('praktik_mode', $terpisah ? 'terpisah' : 'gabung') === 'terpisah')>
                                    <span>
                                        <strong>Dipisah (bobot sendiri)</strong>
                                        <small>Praktik punya persentase sendiri: Tugas, Praktik, STS, SAS, dan Sikap.</small>
                                    </span>
                                </label>
                            </div>

                            <div class="opsi-box">
                                <h4><i class="bi bi-emoji-smile" style="color: var(--ocean-blue, #1d4ed8);"></i> Sikap &amp; Keaktifan</h4>
                                <label class="opsi-radio">
                                    <input type="radio" name="sikap_mode" value="include" @checked(old('sikap_mode', $include ? 'include' : 'tambahan') === 'include')>
                                    <span>
                                        <strong>Termasuk dalam 100%</strong>
                                        <small>Sikap/keaktifan punya persentase sendiri bersama komponen lainnya.</small>
                                    </span>
                                </label>
                                <label class="opsi-radio">
                                    <input type="radio" name="sikap_mode" value="tambahan" @checked(old('sikap_mode', $include ? 'include' : 'tambahan') === 'tambahan')>
                                    <span>
                                        <strong>Nilai tambahan (di luar 100%)</strong>
                                        <small>Komponen lain berjumlah 100%; keaktifan menambah poin ke nilai akhir (maks. 100).</small>
                                    </span>
                                </label>
                            </div>

                            <div class="opsi-box">
                                <h4><i class="bi bi-patch-question" style="color: var(--ocean-blue, #1d4ed8);"></i> Nilai Quiz</h4>
                                <label class="opsi-radio">
                                    <input type="radio" name="quiz_mode" value="nonaktif" @checked(old('quiz_mode', $quizMode) === 'nonaktif')>
                                    <span><strong>Tidak dihitung</strong><small>Quiz hanya untuk latihan; tidak masuk nilai akhir.</small></span>
                                </label>
                                <label class="opsi-radio">
                                    <input type="radio" name="quiz_mode" value="include" @checked(old('quiz_mode', $quizMode) === 'include')>
                                    <span><strong>Termasuk dalam 100%</strong><small>Rata-rata nilai quiz punya persentase sendiri bersama komponen lain.</small></span>
                                </label>
                                <label class="opsi-radio">
                                    <input type="radio" name="quiz_mode" value="tambahan" @checked(old('quiz_mode', $quizMode) === 'tambahan')>
                                    <span><strong>Nilai tambahan (di luar 100%)</strong><small>Rata-rata quiz menambah poin ke nilai akhir, dengan persen &amp; batas poin yang Anda atur.</small></span>
                                </label>
                            </div>
                        </div>

                        <div class="bobot-grid" id="bobotGrid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                            <!-- Input Bobot Tugas -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span id="labelTugas">Tugas Harian</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_tugas" class="field-input-number bobot-input"
                                       value="{{ old('bobot_tugas', $bobot->bobot_tugas ?? 30) }}" min="0" max="100" required>
                            </div>

                            <!-- Input Bobot Praktik (hanya mode terpisah) -->
                            <div class="input-control-box" id="cellPraktik">
                                <label class="label-custom">
                                    <span>Praktik</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_praktik" class="field-input-number bobot-input" id="inpPraktik"
                                       value="{{ old('bobot_praktik', $bobot->bobot_praktik ?? 0) }}" min="0" max="100">
                            </div>

                            <!-- Input Bobot STS -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>STS (Tengah Sem.)</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_sts" class="field-input-number bobot-input"
                                       value="{{ old('bobot_sts', $bobot->bobot_sts ?? 25) }}" min="0" max="100" required>
                            </div>

                            <!-- Input Bobot SAS -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>SAS (Akhir Sem.)</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_sas" class="field-input-number bobot-input"
                                       value="{{ old('bobot_sas', $bobot->bobot_sas ?? 30) }}" min="0" max="100" required>
                            </div>

                            <!-- Input Bobot Sikap (hanya mode include) -->
                            <div class="input-control-box" id="cellSikap">
                                <label class="label-custom">
                                    <span>Sikap & Keaktifan</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_sikap" class="field-input-number bobot-input" id="inpSikap"
                                       value="{{ old('bobot_sikap', $bobot->bobot_sikap ?? 15) }}" min="0" max="100">
                            </div>

                            <!-- Input Bobot Quiz (hanya mode include) -->
                            <div class="input-control-box" id="cellQuiz">
                                <label class="label-custom">
                                    <span>Quiz</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" name="bobot_quiz" class="field-input-number bobot-input" id="inpQuiz"
                                       value="{{ old('bobot_quiz', $bobot->bobot_quiz ?? 0) }}" min="0" max="100">
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

                        {{-- PENGATURAN POIN TAMBAHAN (hanya mode tambahan) --}}
                        <div class="opsi-bonus" id="panelBonus">
                            <strong style="font-size:.9rem;color:#0f172a;"><i class="bi bi-plus-circle-fill" style="color:#1d4ed8;"></i> Pengaturan Poin Tambahan Keaktifan</strong>
                            <div class="opsi-bonus-grid">
                                <div class="input-control-box">
                                    <label class="label-custom">
                                        <span>Kontribusi nilai keaktifan</span>
                                        <span style="color: var(--ocean-blue);">(%)</span>
                                    </label>
                                    <input type="number" name="sikap_bonus_persen" id="inpBonusPersen" class="field-input-number"
                                           value="{{ old('sikap_bonus_persen', $bw['bonus_persen']) }}" min="0" max="100">
                                </div>
                                <div class="input-control-box">
                                    <label class="label-custom">
                                        <span>Maksimal poin tambahan</span>
                                        <span style="color: var(--ocean-blue);">(poin)</span>
                                    </label>
                                    <input type="number" name="sikap_bonus_maks" id="inpBonusMaks" class="field-input-number"
                                           value="{{ old('sikap_bonus_maks', $bw['bonus_maks']) }}" min="0" max="30">
                                </div>
                            </div>
                            <div style="font-size:.78rem;color:#475569;">
                                Rumus: <strong>poin tambahan = nilai keaktifan × kontribusi %</strong>, dibatasi maksimal poin di atas.
                                Nilai keaktifan dari predikat: Sangat Baik 90, Baik 80, Cukup 70, Kurang 60.
                            </div>
                            <div class="opsi-preview" id="previewBonus"></div>
                        </div>

                        {{-- PENGATURAN POIN TAMBAHAN QUIZ (hanya mode tambahan) --}}
                        <div class="opsi-bonus" id="panelBonusQuiz">
                            <strong style="font-size:.9rem;color:#0f172a;"><i class="bi bi-plus-circle-fill" style="color:#1d4ed8;"></i> Pengaturan Poin Tambahan Quiz</strong>
                            <div class="opsi-bonus-grid">
                                <div class="input-control-box">
                                    <label class="label-custom"><span>Kontribusi rata-rata quiz</span><span style="color: var(--ocean-blue);">(%)</span></label>
                                    <input type="number" name="quiz_bonus_persen" id="inpQBonusPersen" class="field-input-number"
                                           value="{{ old('quiz_bonus_persen', $bw['quiz_bonus_persen']) }}" min="0" max="100">
                                </div>
                                <div class="input-control-box">
                                    <label class="label-custom"><span>Maksimal poin tambahan</span><span style="color: var(--ocean-blue);">(poin)</span></label>
                                    <input type="number" name="quiz_bonus_maks" id="inpQBonusMaks" class="field-input-number"
                                           value="{{ old('quiz_bonus_maks', $bw['quiz_bonus_maks']) }}" min="0" max="30">
                                </div>
                            </div>
                            <div style="font-size:.78rem;color:#475569;">Rumus: <strong>poin tambahan = rata-rata nilai quiz × kontribusi %</strong>, dibatasi maksimal poin di atas. Nilai akhir maksimal 100.</div>
                            <div class="opsi-preview" id="previewBonusQuiz"></div>
                        </div>

                        <div class="rule-note">
                            <i class="bi bi-info-circle-fill" style="color: var(--ocean-blue);"></i>
                            <span>Ketentuan: total persentase <strong>komponen yang aktif</strong> harus <strong>persis 100%</strong>.</span>
                        </div>

                        @if ($terpisah && $adaPraktik === 0)
                            <div class="opsi-warn">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Mode praktik terpisah aktif, tetapi belum ada data praktik. Semua siswa mendapat 0 untuk komponen praktik —
                                {{-- FIX #2: nama route sesuai web.php --}}
                                <a href="{{ route('lms.guru.nilai.praktik.index', $pengampuMapel) }}">isi nilai praktik</a> terlebih dulu.
                            </div>
                        @endif
                        @if ($quizAktif && $adaQuiz === 0)
                            <div class="opsi-warn">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Nilai quiz diaktifkan, tetapi belum ada quiz yang terbit untuk kelas ini.
                                <a href="{{ route('lms.guru.quiz.index', $pengampuMapel) }}">Buat quiz</a> dulu.
                            </div>
                        @endif
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
                                    <th style="width: 130px;">{{ $terpisah ? 'Rata Tugas' : ($adaPraktik > 0 ? 'Tugas + Praktik' : 'Rata-rata') }}</th>
                                    @if ($terpisah)
                                        <th style="width: 120px;">Rata Praktik</th>
                                    @endif
                                    <th style="width: 110px;">STS</th>
                                    <th style="width: 110px;">SAS</th>
                                    @if ($quizAktif)
                                        <th style="width: 120px;">Rata Quiz</th>
                                    @endif
                                    <th style="width: 160px;">Predikat Sikap</th>
                                    <th style="min-width: 200px;">Catatan Perkembangan Sikap</th>
                                    @unless ($include)
                                        <th style="width: 110px; text-align: center;">+ Sikap</th>
                                    @endunless
                                    @if ($quizTam)
                                        <th style="width: 110px; text-align: center;">+ Quiz</th>
                                    @endif
                                    <th style="width: 120px; text-align: center;">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapSiswa as $siswaId => $r)
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
                                            @if ($r->rata_komponen_tugas !== null)
                                                <div class="badge-tugas">
                                                    <span>{{ number_format($r->rata_komponen_tugas, 1) }}</span>
                                                </div>
                                                <div class="tugas-count-sub">
                                                    @if ($terpisah)
                                                        {{ $r->jumlah_tugas_dinilai }} tugas dinilai
                                                    @else
                                                        {{ $r->jumlah_tugas_dinilai + $r->jumlah_praktik_dinilai }} penilaian
                                                        @if ($r->jumlah_praktik_dinilai > 0)
                                                            ({{ $r->jumlah_praktik_dinilai }} praktik)
                                                        @endif
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                            @endif
                                        </td>
                                        @if ($terpisah)
                                            <td>
                                                @if ($r->rata_praktik !== null)
                                                    <div class="badge-tugas">
                                                        <span>{{ number_format($r->rata_praktik, 1) }}</span>
                                                    </div>
                                                    <div class="tugas-count-sub">{{ $r->jumlah_praktik_dinilai }} praktik</div>
                                                @else
                                                    <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <input type="number" name="ujian[{{ $siswaId }}][sts]" class="table-input-number"
                                                   value="{{ $r->nilai_sts }}" min="0" max="100" step="0.1" placeholder="0.0">
                                        </td>
                                        <td>
                                            <input type="number" name="ujian[{{ $siswaId }}][sas]" class="table-input-number"
                                                   value="{{ $r->nilai_sas }}" min="0" max="100" step="0.1" placeholder="0.0">
                                        </td>
                                        @if ($quizAktif)
                                            <td>
                                                @if ($r->rata_quiz !== null)
                                                    <div class="badge-tugas"><span>{{ number_format($r->rata_quiz, 1) }}</span></div>
                                                    <div class="tugas-count-sub">{{ $r->jumlah_quiz }} quiz</div>
                                                @else
                                                    <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <select name="sikap[{{ $siswaId }}][predikat]" class="table-select-custom">
                                                @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                                    <option value="{{ $predikat }}" @selected(($r->sikap_predikat ?? 'Baik') === $predikat)>
                                                        {{ $predikat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="sikap[{{ $siswaId }}][catatan]" value="{{ $r->sikap_catatan }}"
                                                   class="table-input-text" placeholder="Catatan guru (opsional)...">
                                        </td>
                                        @unless ($include)
                                            <td style="text-align: center;">
                                                @if ($r->sikap_numerik !== null)
                                                    <span class="bonus-pill">+{{ number_format($r->bonus_sikap, 1) }}</span>
                                                @else
                                                    <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                                @endif
                                            </td>
                                        @endunless
                                        @if ($quizTam)
                                            <td style="text-align: center;">
                                                @if ($r->rata_quiz !== null)
                                                    <span class="bonus-pill">+{{ number_format($r->bonus_quiz, 1) }}</span>
                                                @else
                                                    <span style="color: var(--slate-400); font-size: 0.85rem;">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td style="text-align: center;">
                                            <span class="score-pill-final {{ ($r->nilai_akhir ?? 0) >= 85 ? 'score-high' : 'score-mid' }}">
                                                {{ $r->nilai_akhir ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $jumlahKolom }}" class="empty-state-row">
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

    <!-- SCRIPT: opsi penilaian + total bobot -->
    <script>
        (function () {
            const PREDIKAT = @json(\App\Services\Lms\NilaiAkhirService::PREDIKAT_NUMERIK);
            const $ = id => document.getElementById(id);
            const mode = name => document.querySelector('input[name="' + name + '"]:checked').value;

            function atur() {
                const terpisah = mode('praktik_mode') === 'terpisah';
                const include = mode('sikap_mode') === 'include';

                // tampil/sembunyi komponen; input yang disembunyikan tidak dihitung & tidak dikirim
                $('cellPraktik').classList.toggle('is-hidden', !terpisah);
                $('inpPraktik').disabled = !terpisah;
                $('cellSikap').classList.toggle('is-hidden', !include);
                $('inpSikap').disabled = !include;
                $('panelBonus').classList.toggle('is-hidden', include);
                $('inpBonusPersen').disabled = include;
                $('inpBonusMaks').disabled = include;
                $('labelTugas').textContent = terpisah ? 'Tugas Harian' : 'Tugas + Praktik';

                const qm = mode('quiz_mode');
                $('cellQuiz').classList.toggle('is-hidden', qm !== 'include');
                $('inpQuiz').disabled = qm !== 'include';
                $('panelBonusQuiz').classList.toggle('is-hidden', qm !== 'tambahan');
                $('inpQBonusPersen').disabled = qm !== 'tambahan';
                $('inpQBonusMaks').disabled = qm !== 'tambahan';

                // total bobot komponen aktif
                let total = 0;
                document.querySelectorAll('.bobot-input').forEach(el => { if (!el.disabled) total += Number(el.value || 0); });

                const tampil = $('totalBobot'), btn = $('btnSimpanBobot');
                tampil.textContent = total + '%';
                const valid = total === 100;
                tampil.className = 'total-display-value ' + (valid ? 'total-valid' : 'total-invalid');
                btn.disabled = !valid;
                btn.style.opacity = valid ? '1' : '0.6';

                // contoh poin tambahan quiz
                if (qm === 'tambahan') {
                    const qp = Number($('inpQBonusPersen').value || 0), qk = Number($('inpQBonusMaks').value || 0);
                    $('previewBonusQuiz').innerHTML = [100, 80, 60].map(n =>
                        '<span>rata-rata quiz ' + n + ' → +' + (Math.round(Math.min(qk, n * qp / 100) * 10) / 10) + ' poin</span>').join('');
                }

                // contoh poin tambahan
                if (!include) {
                    const persen = Number($('inpBonusPersen').value || 0);
                    const maks = Number($('inpBonusMaks').value || 0);
                    $('previewBonus').innerHTML = Object.entries(PREDIKAT).map(([nama, nilai]) => {
                        const poin = Math.min(maks, nilai * persen / 100);
                        return '<span>' + nama + ' (' + nilai + ') → +' + (Math.round(poin * 10) / 10) + ' poin</span>';
                    }).join('');
                }
            }

            document.querySelectorAll('input[name="praktik_mode"], input[name="sikap_mode"], input[name="quiz_mode"]').forEach(el => el.addEventListener('change', atur));
            document.querySelectorAll('.bobot-input, #inpBonusPersen, #inpBonusMaks, #inpQBonusPersen, #inpQBonusMaks').forEach(el => el.addEventListener('input', atur));
            atur();
        })();
    </script>
</body>
</html>