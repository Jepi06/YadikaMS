<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal SMK Yadika Soreang – Sekolah Berkarakter &amp; Berprestasi</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/mainDashboard.css" />
</head>

<body>
    <!-- ── NAVBAR ── -->
    <header class="header" id="navbar">
        <div class="container">
            <div class="nav-inner">
                <!-- Brand Logo -->
                <a class="brand-logo" href="#home">
                    <div class="brand-badge">
                        {{-- <img src="img/logo_Yadika.jpg" alt="Logo SMK Yadika"> --}}
                        Y
                    </div>
                    <div>
                        <span class="brand-title">Portal SMK Yadika Soreang</span>
                        <span class="brand-tagline">Sekolah Berkarakter &amp; Berprestasi</span>
                    </div>
                </a>
                <!-- Desktop Nav Links -->
                <nav class="nav-links">
                    <a class="nav-link" href="#layanan">Layanan</a>
                    <a class="nav-link" href="#jurusan">Jurusan</a>
                </nav>
                <!-- Action CTA Buttons -->
                <div class="nav-actions">
                    <a class="btn-lms" href="/lms">
                        <svg fill="none" height="18" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24"
                            width="18">
                            <rect height="14" rx="2" width="20" x="2" y="3"></rect>
                            <path d="M2 20h20"></path>
                        </svg>
                        <span>Masuk LMS</span>
                    </a>
                    <a class="btn-spmb" href="/spmb">
                        <svg fill="currentColor" height="18" viewbox="0 0 24 24" width="18">
                            <path
                                d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zm0 8.82L4.2 9 12 4.77 19.8 9 12 11.82zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z">
                            </path>
                        </svg>
                        <span>Daftar SMBP</span>
                    </a>
                </div>
                <!-- Mobile Hamburger Toggle -->
                <button aria-label="Toggle navigation" class="btn-menu-toggle" id="menuToggle" type="button">
                    <svg fill="none" height="26" id="menuIcon" stroke="currentColor" stroke-width="2"
                        viewbox="0 0 24 24" width="26">
                        <path d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Drawer -->
        <div class="mobile-drawer" id="mobileMenu">
            <a href="#layanan">Layanan Digital</a>
            <a href="#jurusan">Program Keahlian</a>
            <a href="/lms" style="color: var(--primary-700); font-weight:700;">Masuk LMS EduCore</a>
            <a href="/spmb" style="color: var(--primary-700); font-weight:700;">Daftar SMBP 2025/2026</a>
        </div>
    </header>
    <!-- ── HERO SECTION ── -->
    <section class="hero" id="home">
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="container">
            <div class="hero-grid">
                <!-- Hero Left -->
                <div class="hero-left">
                    <div class="hero-tag">
                        <span class="pulse-dot"></span>
                        <span>Penerimaan Siswa Baru: Tahun Ajaran 2025/2026</span>
                    </div>
                    <h1 class="hero-heading">
                        Selamat Datang di Portal <br />
                        <span>SMK Yadika Soreang</span>
                    </h1>
                    <p class="hero-desc">
                        Sekolah Menengah Kejuruan unggulan di Soreang, Kabupaten Bandung — membangun generasi
                        berkarakter, kompeten teknologi, dan berdaya saing global melalui ekosistem vokasi terpadu.
                    </p>
                    <div class="hero-buttons">
                        <a class="btn-gold" href="/spmb">
                            <svg fill="currentColor" height="18" viewbox="0 0 24 24" width="18">
                                <path
                                    d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zm0 8.82L4.2 9 12 4.77 19.8 9 12 11.82zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z">
                                </path>
                            </svg>
                            <span>Daftar SMBP Sekarang</span>
                        </a>
                        <a class="btn-glass" href="/lms">
                            <svg fill="none" height="18" stroke="currentColor" stroke-width="2"
                                viewbox="0 0 24 24" width="18">
                                <rect height="14" rx="2" width="20" x="2" y="3"></rect>
                                <path d="M2 20h20"></path>
                            </svg>
                            <span>Masuk LMS Portal</span>
                        </a>
                        <a class="btn-link-clean" href="#layanan">
                            <span>Jelajahi Layanan</span>
                            <span>→</span>
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div>
                            <div class="stat-value gold">15+</div>
                            <div class="stat-label">Tahun Pengabdian</div>
                        </div>
                        <div>
                            <div class="stat-value blue">3</div>
                            <div class="stat-label">Program Keahlian</div>
                        </div>
                        <div>
                            <div class="stat-value green">95%</div>
                            <div class="stat-label">Keterserapan Kerja</div>
                        </div>
                    </div>
                </div>
                <!-- Hero Right (Glassmorphism Portal Card) -->
                <div>
                    <div class="glass-panel">
                        <div class="panel-header">
                            <div>
                                <div class="panel-header-title">
                                    <svg fill="var(--amber-400)" height="20" viewbox="0 0 24 24" width="20">
                                        <path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zm-9 9h7v7H4v-7zm9 0h7v7h-7v-7z"></path>
                                    </svg>
                                    <span>Akses Layanan Digital</span>
                                </div>
                                <div class="panel-header-subtitle">Portal terintegrasi siswa, pendidik &amp; orang tua
                                </div>
                            </div>
                            <span class="badge-sso">Single Sign-On</span>
                        </div>
                        <div class="service-row-list">
                            <a class="service-row-card" href="/spmb">
                                <div class="service-icon-wrap icon-blue">🎓</div>
                                <div class="service-info">
                                    <div class="service-name">
                                        <span>SPMB Online</span>
                                        <span>›</span>
                                    </div>
                                    <div class="service-sub">Seleksi Masuk Berbasis Prestasi 2025</div>
                                </div>
                            </a>
                            <a class="service-row-card" href="/pkl">
                                <div class="service-icon-wrap icon-green">🏭</div>
                                <div class="service-info">
                                    <div class="service-name">
                                        <span>PKL &amp; Magang Industri</span>
                                        <span>›</span>
                                    </div>
                                    <div class="service-sub">Jurnal digital &amp; monitoring mitra DU/DI</div>
                                </div>
                            </a>
                            <a class="service-row-card" href="/lms">
                                <div class="service-icon-wrap icon-purple">💻</div>
                                <div class="service-info">
                                    <div class="service-name">
                                        <span>LMS EduCore Yadika</span>
                                        <span>›</span>
                                    </div>
                                    <div class="service-sub">Presensi QR, modul ajar, dan tugas online</div>
                                </div>
                            </a>
                        </div>
                        <div class="panel-footer-note">
                            <svg fill="currentColor" viewbox="0 0 20 20">
                                <path clip-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    fill-rule="evenodd"></path>
                            </svg>
                            <span>Terintegrasi resmi dengan BBPPMPV BMTI Kemendikdasmen</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ── MARQUEE STRIP ── -->
    <div class="marquee-container">
        <div class="marquee-track">
            <span class="marquee-item">⭐ SMBP 2025/2026 Resmi Dibuka</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">💼 Pendaftaran PKL Semester Ganjil Mitra Industri</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">⚙️ Jurusan PPLG – Akuntansi – Perhotelan</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">🏆 Juara 2 Lomba Web Design Tingkat Provinsi</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">💻 LMS EduCore Siap Diakses 24/7</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">⭐ SMBP 2025/2026 Resmi Dibuka</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">💼 Pendaftaran PKL Semester Ganjil Mitra Industri</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">⚙️ Jurusan PPLG – Akuntansi – Perhotelan</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">🏆 Juara 2 Lomba Web Design Tingkat Provinsi</span>
            <span class="marquee-dot">•</span>
            <span class="marquee-item">💻 LMS EduCore Siap Diakses 24/7</span>
        </div>
    </div>
    <!-- ── TIGA LAYANAN UNGGULAN ── -->
    <section class="section-py bg-dots" id="layanan">
        <div class="container">
            <div class="section-header">
                <span class="pill-badge">Layanan Digital Terpadu</span>
                <h2 class="section-title">
                    Tiga Layanan Unggulan <br />
                    <span>SMK Yadika Soreang</span>
                </h2>
                <p class="section-desc">
                    Akselerasi proses pembelajaran kejuruan, penempatan magang dunia usaha/dunia industri (DU/DI), dan
                    penerimaan siswa baru dalam satu kesatuan sistem modern.
                </p>
            </div>
            <div class="cards-grid-3">
                <!-- Card 1: SPMB -->
                <div class="card-service">
                    <div>
                        <div class="card-header-banner banner-blue">
                            <div class="card-emoji-box">🎓</div>
                            <h3 class="card-header-title">SPMB Online</h3>
                            <p class="card-header-sub">Seleksi Masuk Berbasis Prestasi &amp; Reguler</p>
                        </div>
                        <div class="card-body">
                            <ul class="checklist">
                                <li>
                                    <svg class="check-icon icon-c-blue" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Pendaftaran online 24 jam tanpa antre fisik</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-blue" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Seleksi transparan berbasis nilai rapor &amp; sertifikat kejuaraan</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-blue" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Notifikasi hasil verifikasi kelulusan real-time</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-blue" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Validasi dokumen digital &amp; sistem pembayaran terpadu</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn-card-action btn-act-blue" href="/spmb">
                            <span>Daftar SPMB Sekarang</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
                <!-- Card 2: PKL -->
                <div class="card-service">
                    <div>
                        <div class="card-header-banner banner-green">
                            <div class="card-emoji-box">🏭</div>
                            <h3 class="card-header-title">PKL / Magang Industri</h3>
                            <p class="card-header-sub">Praktik Kerja Lapangan Vokasi Unggulan</p>
                        </div>
                        <div class="card-body">
                            <ul class="checklist">
                                <li>
                                    <svg class="check-icon icon-c-green" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Jejaring penempatan di 50+ perusahaan mitra industri</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-green" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Monitoring absensi geolokasi &amp; jurnal kegiatan digital</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-green" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Evaluasi kinerja dan sertifikat langsung dari DU/DI</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-green" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Didampingi instruktur kejuruan &amp; mentor BBPPMPV BMTI</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn-card-action btn-act-green" href="/pkl">
                            <span>Ajukan Penempatan PKL</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
                <!-- Card 3: LMS -->
                <div class="card-service">
                    <div>
                        <div class="card-header-banner banner-indigo">
                            <div class="card-emoji-box">💻</div>
                            <h3 class="card-header-title">LMS EduCore Yadika</h3>
                            <p class="card-header-sub">Learning Management System Terintegrasi</p>
                        </div>
                        <div class="card-body">
                            <ul class="checklist">
                                <li>
                                    <svg class="check-icon icon-c-indigo" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Presensi barcode QR dinamis berbasis session token</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-indigo" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Modul ajar kurikulum merdeka &amp; video interaktif</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-indigo" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Pengumpulan tugas individu &amp; kelompok dengan grading guru</span>
                                </li>
                                <li>
                                    <svg class="check-icon icon-c-indigo" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewbox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Dashboard monitoring presensi real-time untuk wali kelas</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn-card-action btn-act-indigo" href="/lms">
                            <span>Masuk Portal Belajar LMS</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ── PROGRAM KEAHLIAN / JURUSAN ── -->
    <section class="section-py section-jurusan" id="jurusan">
        <div class="container">
            <div class="header-split">
                <div>
                    <span class="pill-badge">Program Keahlian</span>
                    <h2 class="section-title">
                        Pilih Jurusan yang Tepat <br />
                        <span>Untuk Masa Depan Kariermu</span>
                    </h2>
                </div>
                <p class="section-desc">
                    Kurikulum berbasis standar industri terkini, didukung fasilitas laboratorium modern dan pengajar
                    praktisi profesional.
                </p>
            </div>
            <div class="cards-grid-3">
                <!-- Jurusan 1: PPLG -->
                <div class="jurusan-card">
                    <div>
                        <div class="jurusan-illustration bg-ill-blue">
                            💻
                            <span class="jurusan-tag-float">Unggulan IT</span>
                        </div>
                        <div class="jurusan-content">
                            <div class="jurusan-cat blue">Teknologi Informasi</div>
                            <h3 class="jurusan-title">Pengembangan Perangkat Lunak &amp; Gim (PPLG)</h3>
                            <p class="jurusan-desc">
                                Kuasai pembangunan aplikasi web modern, pemrograman mobile Android/iOS, basis data SQL,
                                serta game development dengan standar teknologi industri masa kini.
                            </p>
                            <div class="tags-cluster">
                                <span class="tag-bubble">Web Developer</span>
                                <span class="tag-bubble">Mobile Apps</span>
                                <span class="tag-bubble">UI/UX Designer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Jurusan 2: AKL -->
                <div class="jurusan-card">
                    <div>
                        <div class="jurusan-illustration bg-ill-green">
                            📊
                            <span class="jurusan-tag-float">Finansial &amp; Bisnis</span>
                        </div>
                        <div class="jurusan-content">
                            <div class="jurusan-cat green">Bisnis &amp; Manajemen</div>
                            <h3 class="jurusan-title">Akuntansi &amp; Keuangan Lembaga (AKL)</h3>
                            <p class="jurusan-desc">
                                Menguasai pembukuan komputer akuntansi (MYOB/Accurate), pengelolaan perpajakan digital,
                                perbankan syariah/konvensional, serta audit keuangan lembaga.
                            </p>
                            <div class="tags-cluster">
                                <span class="tag-bubble">Staff Akuntan</span>
                                <span class="tag-bubble">Perbankan</span>
                                <span class="tag-bubble">Konsultan Pajak</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Jurusan 3: Perhotelan -->
                <div class="jurusan-card">
                    <div>
                        <div class="jurusan-illustration bg-ill-orange">
                            🏨
                            <span class="jurusan-tag-float">Pariwisata Global</span>
                        </div>
                        <div class="jurusan-content">
                            <div class="jurusan-cat orange">Pariwisata &amp; Hospitality</div>
                            <h3 class="jurusan-title">Perhotelan &amp; Hospitality</h3>
                            <p class="jurusan-desc">
                                Mempersiapkan tenaga ahli di bidang front office, housekeeping berstandar internasional,
                                food &amp; beverage service, serta manajemen perhotelan berbintang.
                            </p>
                            <div class="tags-cluster">
                                <span class="tag-bubble">Front Office</span>
                                <span class="tag-bubble">F&amp;B Service</span>
                                <span class="tag-bubble">Housekeeping</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ── CALL TO ACTION BANNER ── -->
    <section class="cta-banner">
        <div class="cta-inner">
            <span class="badge-cta">Penerimaan Peserta Didik Baru</span>
            <h2 class="cta-heading">Bergabung Bersama SMK Yadika Soreang – Wujudkan Karier Impianmu!</h2>
            <p class="cta-subtext">
                Daftarkan diri lewat jalur SPMB sekarang juga. Kuota kelas kejuruan terbatas — jadilah bagian dari
                generasi vokasi unggulan.
            </p>
            <div class="cta-buttons">
                <a class="btn-gold" href="/spmb">
                    <svg fill="currentColor" height="18" viewbox="0 0 24 24" width="18">
                        <path
                            d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zm0 8.82L4.2 9 12 4.77 19.8 9 12 11.82zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z">
                        </path>
                    </svg>
                    <span>Daftar SPMB Online Sekarang</span>
                </a>
                <a class="btn-glass" href="https://wa.me/6281234567890" rel="noopener noreferrer" target="_blank">
                    <svg fill="currentColor" height="18" viewbox="0 0 24 24" width="18">
                        <path
                            d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.07 16.3C4.24 14.98 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z">
                        </path>
                    </svg>
                    <span>Konsultasi via WhatsApp</span>
                </a>
            </div>
        </div>
    </section>
    <!-- ── FOOTER ── -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col Brand -->
                <div class="footer-brand">
                    <div class="brand-logo">
                        <div class="brand-badge">Y</div>
                        <div>
                            <span class="brand-title" style="color: var(--white);">SMK Yadika Soreang</span>
                            <span class="brand-tagline">Sekolah Berkarakter &amp; Berprestasi</span>
                        </div>
                    </div>
                    <p style="line-height:1.6; max-width: 360px;">
                        Lembaga pendidikan kejuruan unggulan di Soreang, Kabupaten Bandung. Mencetak lulusan kompeten,
                        berkarakter mulia, dan siap terjun langsung ke industri digital dan jasa.
                    </p>
                    <div class="social-row">
                        <a aria-label="Facebook" class="social-box" href="#">f</a>
                        <a aria-label="Instagram" class="social-box" href="#">📸</a>
                        <a aria-label="YouTube" class="social-box" href="#">▶</a>
                        <a aria-label="TikTok" class="social-box" href="#">♪</a>
                    </div>
                </div>
                <!-- Col 1 -->
                <div class="footer-col">
                    <h5>Layanan</h5>
                    <ul>
                        <li><a href="/spmb">Portal SPMB Online</a></li>
                        <li><a href="/pkl">Sistem PKL &amp; Magang</a></li>
                        <li><a href="/lms">LMS EduCore</a></li>
                        <li><a href="#">E-Rapor Vokasi</a></li>
                        <li><a href="#">Layanan Pengaduan</a></li>
                    </ul>
                </div>
                <!-- Col 2 -->
                <div class="footer-col">
                    <h5>Kejuruan</h5>
                    <ul>
                        <li><a href="#jurusan">PPLG (Rekayasa Perangkat Lunak)</a></li>
                        <li><a href="#jurusan">Akuntansi &amp; Keuangan Lembaga</a></li>
                        <li><a href="#jurusan">Perhotelan &amp; Pariwisata</a></li>
                        <li><a href="#">Ekstrakurikuler Unggulan</a></li>
                        <li><a href="#">Laboratorium Praktikum</a></li>
                    </ul>
                </div>
                <!-- Col 3 -->
                <div class="footer-col">
                    <h5>Informasi</h5>
                    <ul>
                        <li><a href="#sambutan">Profil Sekolah</a></li>
                        <li><a href="#">Kalender Akademik</a></li>
                        <li><a href="#">Berita &amp; Kegiatan</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div>© 2025 SMK Yadika Soreang. Hak Cipta Dilindungi Undang-Undang.</div>
                <div>Dikembangkan dengan ❤️ • Kolaborasi bersama BBPPMPV BMTI Kemendikdasmen</div>
            </div>
        </div>
    </footer>
    <!-- ── FLOATING WHATSAPP BUTTON ── -->
    <a class="wa-float" href="https://wa.me/6281234567890" rel="noopener noreferrer" target="_blank"
        title="Chat Petugas SPMB via WhatsApp">
        💬
    </a>
    <!-- Vanilla Javascript -->
    <script>
        const navbar = document.getElementById('navbar');
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIcon = document.getElementById('menuIcon');

        // Scroll glass effect
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Toggle mobile drawer
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            if (isOpen) {
                menuIcon.innerHTML = '<path d="M6 18L18 6M6 6l12 12"/>';
            } else {
                menuIcon.innerHTML = '<path d="M4 6h16M4 12h16M4 18h16"/>';
            }
        });

        // Close mobile drawer on item click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                menuIcon.innerHTML = '<path d="M4 6h16M4 12h16M4 18h16"/>';
            });
        });
    </script>
</body>

</html>
