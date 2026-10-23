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
</head>
<body>

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
                        <div class="bobot-grid">
                            <!-- Input Bobot Tugas -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>Tugas Harian</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" 
                                       name="bobot_tugas" 
                                       class="field-input-number bobot-input" 
                                       value="{{ $bobot->bobot_tugas ?? 30 }}" 
                                       min="0" 
                                       max="100" 
                                       required>
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
                                       value="{{ $bobot->bobot_sts ?? 25 }}" 
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
                                       value="{{ $bobot->bobot_sas ?? 30 }}" 
                                       min="0" 
                                       max="100" 
                                       required>
                            </div>

                            <!-- Input Bobot Sikap -->
                            <div class="input-control-box">
                                <label class="label-custom">
                                    <span>Sikap & Keaktifan</span>
                                    <span style="color: var(--ocean-blue);">(%)</span>
                                </label>
                                <input type="number" 
                                       name="bobot_sikap" 
                                       class="field-input-number bobot-input" 
                                       value="{{ $bobot->bobot_sikap ?? 15 }}" 
                                       min="0" 
                                       max="100" 
                                       required>
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
                            <span>Ketentuan: Akumulasi total keempat persentase bobot harus <strong>persis 100%</strong>.</span>
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
                                    <th style="width: 110px;">STS</th>
                                    <th style="width: 110px;">SAS</th>
                                    <th style="width: 160px;">Predikat Sikap</th>
                                    <th style="min-width: 200px;">Catatan Perkembangan Sikap</th>
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
                                            @if ($r->rata_tugas !== null)
                                                <div class="badge-tugas">
                                                    <span>{{ number_format($r->rata_tugas, 1) }}</span>
                                                </div>
                                                <div class="tugas-count-sub">{{ $r->jumlah_tugas_dinilai }} tugas dinilai</div>
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
                                            <select name="sikap[{{ $siswaId }}][predikat]" class="table-select-custom">
                                                @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                                    <option value="{{ $predikat }}" @selected(($r->sikap_predikat ?? 'Baik') === $predikat)>
                                                        {{ $predikat }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="empty-state-row">
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

    <!-- SCRIPT VANILLA JS (Perhitungan Otomatis Bobot) -->
    <script>
        (function() {
            function hitungTotalBobot() {
                const inputs = document.querySelectorAll('.bobot-input');
                let total = 0;
                inputs.forEach(el => total += Number(el.value || 0));
                
                const displayEl = document.getElementById('totalBobot');
                const btnSubmit = document.getElementById('btnSimpanBobot');
                
                if (displayEl) {
                    displayEl.textContent = total + '%';
                    if (total === 100) {
                        displayEl.className = 'total-display-value total-valid';
                        if (btnSubmit) {
                            btnSubmit.disabled = false;
                            btnSubmit.style.opacity = '1';
                        }
                    } else {
                        displayEl.className = 'total-display-value total-invalid';
                        if (btnSubmit) {
                            btnSubmit.disabled = true;
                            btnSubmit.style.opacity = '0.6';
                        }
                    }
                }
            }

            document.querySelectorAll('.bobot-input').forEach(el => {
                el.addEventListener('input', hitungTotalBobot);
            });

            // Jalankan saat load awal
            hitungTotalBobot();
        })();
    </script>
</body>
</html>
