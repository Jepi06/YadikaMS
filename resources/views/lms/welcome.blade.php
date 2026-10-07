<!DOCTYPE html>
<html lang="id" style="">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>LMS - SMK Yadika Soreang</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=Space+Grotesk:wght@500;700;800&amp;display=swap"
        rel="stylesheet">
    <link href="/css/homeLms.css" rel="stylesheet">
</head>

<body>
    <!-- NAVBAR -->
    <header class="glass-nav">
        <div class="container">
            <div class="nav-inner">
                <div class="nav-left">
                    <!-- Hamburger: hanya tampil di mobile -->
                    <details class="mobile-menu" id="mobileMenu">
                        <summary aria-label="Buka Menu Navigasi" class="mobile-menu-btn">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                        </summary>
                        <div class="mobile-menu-panel">
                            <a href="#fitur" class="">Fitur Unggulan</a>
                            <a href="#alur-presensi" class="">Presensi Barcode</a>
                            <a href="#jurusan" class="">Jurusan</a>
                            <div class="mobile-menu-divider"></div>
                            <div class="mobile-menu-status">
                                <span class="pulse-dot"></span>
                                Server Aktif (T.A 2024/2025)
                            </div>
                        </div>
                    </details>
                    <!-- Brand -->
                    <a class="brand-link" href="#home">
                        <div class="brand-icon-box">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                                <path
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                <path d="M12 14v7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                </path>
                            </svg>
                        </div>
                        <div class="brand-text">
                            <span class="brand-name">LMS Yadika Soreang</span>
                            <span class="brand-subtitle">SMK Yadika Soreang</span>
                        </div>
                    </a>
                </div>
                <!-- Navigasi desktop: hanya tampil di desktop -->
                <nav class="nav-links">
                    <a class="nav-link" href="#fitur">Fitur Unggulan</a>
                    <a class="nav-link" href="#alur-presensi">Presensi Barcode</a>
                    <a class="nav-link" href="#jurusan">Jurusan</a>
                    <span class="server-status-badge">
                        <span class="pulse-dot"></span>
                        Server Aktif (T.A 2024/2025)
                    </span>
                </nav>
                <!-- CTA -->
                <div class="nav-right">
                    <a class="btn-login" href="{{ route('lms.login') }}">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                        <span class="">Masuk</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <!-- HERO SECTION -->
    <style>
        @media (max-width: 480px) {
            .container {
                padding-left: 1.125rem !important;
                padding-right: 1.125rem !important;
            }
        }

        @media (max-width: 360px) {
            .container {
                padding-left: 1.125rem !important;
                padding-right: 1.125rem !important;
            }

            .nav-inner {
                padding-left: 0.125rem;
                padding-right: 0.125rem;
            }

            .mockup-wrapper,
            .summary-banner-card,
            .feature-card,
            .scanner-mockup-card,
            .jurusan-card,
            .cta-card {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
        }
    </style>
    <section class="hero-section" id="home">
        <!-- Atmospheric glows -->
        <div class="ambient-glow-1"></div>
        <div class="ambient-glow-2"></div>
        <div class="container">
            <div class="hero-grid">
                <!-- Kiri: Copywriting & CTA -->
                <div class="hero-left">
                    <div class="hero-badge">
                        <span class="pulse-dot pulse-dot-sky"></span>
                        SMK Yadika Soreang — Terakreditasi "A"
                    </div>
                    <div class="hero-heading-box">
                        <h1 class="hero-title">
                            Learning Management System
                            <span class="gradient-text-sky">Cerdas, Cepat &amp; Terintegrasi</span>
                        </h1>
                    </div>
                    <p class="hero-desc">
                        Satu portal untuk materi daring, presensi QR berbasis GPS, dan rekap nilai
                        otomatis, dirancang untuk siswa, guru, dan sekolah.
                    </p>
                    <div class="hero-cta-group">
                        <a class="btn-hero-primary" href="{{ route('lms.login') }}"
                            style="width: auto; max-width: min(100%, 22rem); margin: 0px auto; display: inline-flex;">
                            <span class="">Masuk ke LMS Sekarang</span>
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2.5"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="credibility-grid">
                        <div class="credibility-card">
                            <div class="stat-number">1.200+</div>
                            <div class="stat-label">Siswa &amp; Taruna Aktif</div>
                        </div>
                        <div class="credibility-card">
                            <div class="stat-number">60+</div>
                            <div class="stat-label">Guru &amp; Mentor Ahli</div>
                        </div>
                        <div class="credibility-card">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Daring &amp; Real-Time</div>
                        </div>
                    </div>
                </div>
                <!-- Kanan: Mockup dashboard -->
                <div class="mockup-wrapper">
                    <div class="mockup-backglow"></div>
                    <div class="mockup-panel">
                        <div class="mockup-top-bar">
                            <div class="browser-dots">
                                <div class="browser-dot dot-red"></div>
                                <div class="browser-dot dot-amber"></div>
                                <div class="browser-dot dot-emerald"></div>
                                <span class="browser-url">lms.yadika-ms.my.id</span>
                            </div>
                            <span class="status-badge-online">
                                <span class="dot-online"></span>
                                Sistem Online
                            </span>
                        </div>
                        <div class="portal-banner">
                            <div>
                                <span class="banner-tag">Portal Terpadu Pembelajaran</span>
                                <h4 class="portal-banner-title">LMS SMK Yadika Soreang</h4>
                                <p class="portal-banner-desc">Pusat Akses Akademik, Modul Daring &amp; Presensi</p>
                            </div>
                            <div class="banner-icon">
                                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"></path>
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mockup-hub-matrix">
                            <div class="hub-matrix-card">
                                <div class="hub-icon-round hub-icon-sky">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>
                                <div class="hub-label">Modul Materi</div>
                                <div class="hub-sub">Multi-format</div>
                            </div>
                            <div class="hub-matrix-card">
                                <div class="hub-icon-round hub-icon-emerald">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>
                                <div class="hub-label">QR Presensi</div>
                                <div class="hub-sub">Real-Time GPS</div>
                            </div>
                            <div class="hub-matrix-card">
                                <div class="hub-icon-round hub-icon-sky">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>
                                <div class="hub-label">Rekap Nilai</div>
                                <div class="hub-sub">Ekspor Otomatis</div>
                            </div>
                        </div>
                        <div class="barcode-mini-card">
                            <div class="barcode-mini-head">
                                <div class="barcode-title-row">
                                    <span class="ping-dot"></span>
                                    <span class="barcode-title-text">Pintu Presensi Digital Cepat</span>
                                </div>
                                <span class="badge-tervalidasi">Tervalidasi Aktif</span>
                            </div>
                            <div class="barcode-inner-content">
                                <div class="qr-thumb-box">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M2 2h7v7H2V2zm2 2v3h3V4H4zm9-2h7v7h-7V2zm2 2v3h3V4h-3zM2 13h7v7H2v-7zm2 2v3h3v-3H4zm11 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm2-4h2v2h-2v-2zm-6 0h2v2h-2v-2zm4-6h2v2h-2V7zm-2 2h2v2h-2V9zm4 0h2v2h-2V9z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="barcode-text">
                                    <p class="barcode-text-main">Scan Barcode melalui Smartphone Siswa</p>
                                    <div class="barcode-text-check">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"></path>
                                        </svg>
                                        <span class="">Terhubung ke Semua Konsentrasi Keahlian</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mockup-footer">
                            <span class="mockup-footer-sync">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                                Sinkronisasi Cloud Aktif
                            </span>
                            <span class="mockup-footer-year">T.A 2026/2027</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- HIGHLIGHT BANNER: KELEBIHAN UNTUK GURU & SISWA -->
    <section class="summary-banner-section">
        <div class="container">
            <div class="summary-banner-card">
                <div class="summary-item">
                    <div class="summary-icon-box icon-box-sky">
                        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="summary-title">Materi PDF, Video &amp; Dokumen</h4>
                        <p class="summary-text">Akses modul pembelajaran kapan saja tanpa batas kuota lokal.</p>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-icon-box icon-box-blue">
                        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="summary-title">Dual Presensi (Scan &amp; Manual)</h4>
                        <p class="summary-text">Fleksibel: scan barcode di kelas atau entri manual bagi siswa
                            izin/sakit.</p>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-icon-box icon-box-teal">
                        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="summary-title">Rekap Nilai Otomatis</h4>
                        <p class="summary-text">Ekspor daftar nilai dan persentase kehadiran langsung ke file
                            Excel/PDF.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FITUR UTAMA SECTION -->
    <section class="features-section" id="fitur">
        <div class="container">
            <!-- Section Title Header -->
            <div class="section-header">
                <span class="section-eyebrow">
                    Fasilitas Digital Terlengkap
                </span>
                <h2 class="section-title">
                    Fitur Utama untuk Mendukung Belajar Modern
                </h2>
                <p class="section-desc">
                    Dirancang khusus untuk kebutuhan guru dan siswa jurusan Rekayasa Perangkat Lunak (PPLG) serta
                    seluruh program keahlian di SMK Yadika Soreang.
                </p>
            </div>
            <!-- Feature Cards Grid (3 Columns) -->
            <div class="features-grid">
                <!-- Feature 1: Materi & Tugas -->
                <div class="feature-card">
                    <div>
                        <div class="feature-icon-box icon-box-gradient-dark">
                            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="feature-heading">Materi &amp; Tugas Interaktif</h3>
                        <p class="feature-body">
                            Guru dapat mengunggah silabus, modul materi (PDF, PPT, link video), serta penugasan
                            terstruktur per kelas. Siswa mengunggah dokumen jawaban secara online dengan timestamp
                            otomatis.
                        </p>
                    </div>
                    <div class="feature-footer">
                        <span class="check-feature">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                            Mendukung Multi-format File
                        </span>
                        <span class="">Mudah &amp; Aman</span>
                    </div>
                </div>
                <!-- Feature 2: Presensi Barcode -->
                <div class="feature-card feature-card-popular">
                    <div class="badge-popular">
                        Populer
                    </div>
                    <div>
                        <div class="feature-icon-box icon-box-gradient-sky">
                            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="feature-heading">Presensi Barcode &amp; Manual</h3>
                        <p class="feature-body">
                            Guru membuka sesi absensi dengan QR code yang berganti secara terenkripsi di proyektor.
                            Siswa cukup melakukan pemindaian melalui ponsel untuk verifikasi hadir secara akurat tanpa
                            titip absen.
                        </p>
                    </div>
                    <div class="feature-footer">
                        <span class="check-feature">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                            Absen Selesai &lt; 5 Detik
                        </span>
                        <span class="">Anti Kecurangan</span>
                    </div>
                </div>
                <!-- Feature 3: Rekap & Penilaian -->
                <div class="feature-card">
                    <div>
                        <div class="feature-icon-box icon-box-gradient-ocean">
                            <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <h3 class="feature-heading">Rekap Nilai &amp; Analitik</h3>
                        <p class="feature-body">
                            Guru menilai tugas dengan rubrik yang transparan dan memantau persentase partisipasi
                            kehadiran kelas secara terstruktur, siap diekspor untuk bahan rapor semesteran.
                        </p>
                    </div>
                    <div class="feature-footer">
                        <span class="check-feature">
                            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            Ekspor Laporan Cepat
                        </span>
                        <span class="">Otomatisasi Penuh</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ALUR PRESENSI & INTERAKTIF STEP SECTION -->
    <section class="steps-section" id="alur-presensi">
        <div class="container">
            <div class="steps-grid">
                <!-- Visual Step Demonstration -->
                <div class="steps-left">
                    <div class="eyebrow-accent">
                        Inovasi Unggulan
                    </div>
                    <h2 class="steps-title">
                        Alur Presensi Barcode Digital dalam 3 Langkah Praktis
                    </h2>
                    <p class="section-desc">
                        Tidak ada lagi waktu belajar terbuang hanya untuk memanggil presensi satu per satu. Dengan
                        sistem kode QR terenkripsi yang dibuat oleh guru, pencatatan hadir selesai dalam hitungan detik.
                    </p>
                    <!-- Steps -->
                    <div class="step-list">
                        <!-- Step 1 -->
                        <div class="step-item">
                            <div class="step-badge badge-navy">
                                1
                            </div>
                            <div>
                                <h4 class="step-heading">Guru Membuka Sesi Pertemuan</h4>
                                <p class="step-text">Guru memilih mata pelajaran dan memproyeksikan Barcode dinamis di
                                    layar depan kelas.</p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="step-item step-item-active">
                            <div class="step-badge badge-sky">
                                2
                            </div>
                            <div>
                                <h4 class="step-heading">Siswa Scan Barcode dari Ponsel</h4>
                                <p class="step-text">Siswa masuk ke akun LMS masing-masing dan mengarahkan kamera
                                    scanner langsung.</p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="step-item">
                            <div class="step-badge badge-emerald">
                                3
                            </div>
                            <div>
                                <h4 class="step-heading">Kehadiran Langsung Tercatat</h4>
                                <p class="step-text">Data siswa langsung berubah status menjadi 'Hadir'. Guru dapat
                                    mengubah siswa yang sakit/izin secara manual.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Side: Graphic Panel Scanner -->
                <div class="steps-right">
                    <div class="scanner-mockup-card">
                        <div class="scanner-backglow"></div>
                        <div class="scanner-top">
                            <div class="scanner-camera-info">
                                <div class="scanner-icon-wrap">
                                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h5 style="font-size: 0.9375rem; font-weight: 700; color: #fff;" class="">
                                        Scanner Terintegrasi</h5>
                                    <p style="font-size: 0.75rem; color: var(--slate-300);" class="">Validasi
                                        Lokasi &amp; Waktu Nyata</p>
                                </div>
                            </div>
                            <span class="badge-gps">GPS Akurat</span>
                        </div>
                        <div class="scanner-target-wrap">
                            <div class="scanner-reticle">
                                <span class="corner-tl"></span>
                                <span class="corner-tr"></span>
                                <span class="corner-bl"></span>
                                <span class="corner-br"></span>
                                <div class="scanner-laser"></div>
                                <div class="scanner-qr-card">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M2 2h7v7H2V2zm2 2v3h3V4H4zm9-2h7v7h-7V2zm2 2v3h3V4h-3zM2 13h7v7H2v-7zm2 2v3h3v-3H4zm11 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm2-4h2v2h-2v-2zm-6 0h2v2h-2v-2zm4-6h2v2h-2V7zm-2 2h2v2h-2V9zm4 0h2v2h-2V9z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: var(--slate-300); padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <span
                                style="display: flex; align-items: center; gap: 0.375rem; color: #6ee7b7; font-weight: 600;"
                                class="">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"></path>
                                </svg>
                                Sesi Terbuka: Sesi 1 PPLG
                            </span>
                            <span style="color: var(--sky-400); font-family: monospace;" class="">TOKEN:
                                #YDK882</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- JURUSAN & KONSENTRASI KEAHLIAN -->
    <section class="jurusan-section" id="jurusan">
        <div class="container">
            <div class="section-header" style="margin-bottom: 3.5rem;">
                <h3 class="section-title">Tersedia untuk Semua Program Keahlian</h3>
                <p class="section-desc">SMK Yadika Soreang memfasilitasi setiap kompetensi dengan modul digital yang
                    relevan dengan industri.</p>
            </div>
            <div class="jurusan-grid">
                <div class="jurusan-card">
                    <div class="jurusan-badge-icon badge-pplg">PPLG</div>
                    <h5 class="jurusan-title">Rekayasa Perangkat Lunak</h5>
                    <p class="jurusan-desc">Coding, Basis Data &amp; Web</p>
                </div>
                <div class="jurusan-card">
                    <div class="jurusan-badge-icon badge-htl">HTL</div>
                    <h5 class="jurusan-title">Perhotelan</h5>
                    <p class="jurusan-desc">Front Office, Tata Graha &amp; Layanan</p>
                </div>
                <div class="jurusan-card">
                    <div class="jurusan-badge-icon badge-akl">AKL</div>
                    <h5 class="jurusan-title">Akuntansi &amp; Keuangan</h5>
                    <p class="jurusan-desc">Spreadsheet, Pembukuan &amp; Perpajakan</p>
                </div>
            </div>
        </div>
    </section>
    <!-- CTA FOOTER HERO -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <div class="cta-inner">
                    <h3 class="cta-heading">Siap Memulai Pembelajaran Hari Ini?</h3>
                    <p class="cta-sub">
                        Gunakan akun siswa atau guru Anda untuk mengakses ruang kelas virtual, modul pelajaran, dan
                        daftar presensi.
                    </p>
                    <div style="padding-top: 0.5rem;">
                        <a class="btn-cta-white" href="{{ route('lms.login') }}">
                            <span class="">Masuk ke Akun LMS</span>
                            <svg class="icon-sm" fill="none" stroke="currentColor" style="color: var(--sky-600);"
                                viewBox="0 0 24 24">
                                <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2.5"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-brand">
                    <div class="footer-logo-box">
                        Y
                    </div>
                    <span class="footer-brand-title">SMK Yadika Soreang</span>
                    <span class="footer-divider">|</span>
                    <span class="footer-sub">Learning Management System</span>
                </div>
                <p class="footer-copyright">
                    © 2026 SMK Yadika Soreang. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </footer>
    <script>
        // Tutup dropdown mobile setelah link diklik
        document.querySelectorAll('#mobileMenu a').forEach(function(a) {
            a.addEventListener('click', function() {
                document.getElementById('mobileMenu').removeAttribute('open');
            });
        });
    </script>

</body>

</html>
