<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - SMK Yadika Soreang</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-navy: #072b4a;
            --ocean-blue: #0284c7;
            --sky-blue: #38bdf8;
            --sky-light: #f0f9ff;
            --emerald-green: #10b981;
            --emerald-glow: rgba(16, 185, 129, 0.15);
            --card-border: rgba(226, 232, 240, 0.85);
            --card-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.12), 0 0 1px 1px rgba(2, 132, 199, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient subtle background glow */
        .ambient-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            background: 
                radial-gradient(circle at 15% 15%, rgba(56, 189, 248, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(2, 132, 199, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 50% 30%, rgba(16, 185, 129, 0.06) 0%, transparent 35%);
        }

        /* Modern Topbar Navbar */
        .spmb-navbar {
            background: linear-gradient(135deg, #072540 0%, #0d3860 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            z-index: 10;
        }

        .brand-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        }

        /* Main Container */
        .page-wrapper {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            padding: 3rem 0 4rem;
        }

        /* Main Success Card */
        .success-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Top decorative ocean gradient bar */
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0284c7, #38bdf8, #10b981);
        }

        /* Success Animated Icon Badge */
        .success-icon-wrapper {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 3px solid #a7f3d0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 12px 28px -6px rgba(16, 185, 129, 0.35);
            animation: pulseSuccessGlow 3s infinite ease-in-out;
        }

        .success-icon-wrapper i {
            font-size: 2.75rem;
            color: #059669;
            filter: drop-shadow(0 2px 4px rgba(5, 150, 105, 0.2));
        }

        @keyframes pulseSuccessGlow {
            0%, 100% {
                box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.35), 0 0 0 0 rgba(16, 185, 129, 0.2);
            }
            50% {
                box-shadow: 0 14px 30px -4px rgba(16, 185, 129, 0.5), 0 0 0 12px rgba(16, 185, 129, 0);
            }
        }

        /* Header typography */
        .title-gradient {
            background: linear-gradient(135deg, #072b4a 0%, #0369a1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        /* Registration Number Box */
        .registration-number-box {
            background: linear-gradient(145deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1.5px dashed #7dd3fc;
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            position: relative;
            transition: all 0.25s ease;
        }

        .registration-number-box:hover {
            border-color: #0284c7;
            background: linear-gradient(145deg, #e0f2fe 0%, #bae6fd 100%);
            box-shadow: 0 8px 24px -6px rgba(2, 132, 199, 0.15);
        }

        .reg-number-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #0369a1;
            display: inline-block;
            text-shadow: 0 1px 2px rgba(3, 105, 161, 0.1);
        }

        .copy-pill-btn {
            background: #ffffff;
            border: 1px solid #bae6fd;
            color: #0284c7;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.08);
        }

        .copy-pill-btn:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            transform: translateY(-1px);
        }

        /* Candidate Details Table Card */
        .details-wrapper {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0.25rem;
        }

        .detail-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-label i {
            color: #0284c7;
            font-size: 1rem;
        }

        .detail-value {
            font-size: 0.925rem;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        /* Status Badge - Menunggu */
        .status-badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.4rem 0.85rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fef3c7;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.1);
        }

        .status-dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
            animation: statusPulse 1.8s infinite ease-in-out;
        }

        @keyframes statusPulse {
            0%, 100% {
                transform: scale(0.95);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.3);
                opacity: 1;
            }
        }

        /* CTA Primary Button */
        .btn-ocean-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-ocean-primary:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -5px rgba(2, 132, 199, 0.55);
        }

        .btn-ocean-primary:active {
            transform: translateY(0);
        }

        /* Action Buttons Row */
        .btn-secondary-action {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.8rem 1.25rem;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-secondary-action:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        /* Guidance Banner */
        .info-step-banner {
            background: #f8fafc;
            border-radius: 14px;
            border-left: 4px solid #0284c7;
            padding: 0.85rem 1rem;
            text-align: left;
        }

        /* Print friendly styles */
        @media print {
            .spmb-navbar, .ambient-mesh, .btn-ocean-primary, .btn-secondary-action, .copy-pill-btn, footer {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .page-wrapper {
                padding: 0;
            }
            .success-card {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }

        /* Footer */
        .spmb-footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            font-size: 0.825rem;
            padding: 1.25rem 0;
        }
    </style>
</head>

<body>
    <!-- Ambient Background Glow -->
    <div class="ambient-mesh"></div>

    <!-- =========================
        NAVBAR PORTAL
    ========================== -->
    <header class="spmb-navbar py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2 m-0 fs-6" href="{{ route('spmb.public.index') }}">
                <div class="brand-logo-icon">
                    <i class="bi bi-mortarboard-fill fs-5"></i>
                </div>
                <div>
                    <div class="lh-1 fw-bold">SPMB SMK Yadika Soreang</div>
                    <small class="text-white-50" style="font-size: 0.72rem; letter-spacing: 0.02em;">Portal Penerimaan Siswa Baru</small>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('spmb.public.index') }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1 px-3 py-1 rounded-pill" style="font-size: 0.8rem;">
                    <i class="bi bi-search"></i>
                    <span>Cek Status</span>
                </a>
            </div>
        </div>
    </header>

    <!-- =========================
        MAIN CONTENT
    ========================== -->
    <main class="page-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-xl-6">
                    
                    <!-- SUCCESS CARD -->
                    <div class="card success-card text-center p-4 p-md-5">
                        
                        <!-- Animated Green Check Badge -->
                        <div class="mb-3">
                            <div class="success-icon-wrapper">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        </div>

                        <!-- Card Header Heading -->
                        <div class="mb-4">
                            <span class="badge bg-emerald-light text-success fw-semibold px-3 py-1 rounded-pill mb-2 border border-success-subtle" style="background-color: #ecfdf5; font-size: 0.78rem;">
                                <i class="bi bi-shield-check me-1"></i> Data Tersimpan di Sistem
                            </span>
                            <h3 class="title-gradient mb-2">Pendaftaran Berhasil!</h3>
                            <p class="text-muted small mb-0 px-md-3">
                                Simpan nomor pendaftaran di bawah ini baik-baik.
                                Nomor ini dipakai untuk cek status kelulusan nanti.
                            </p>
                        </div>

                        <!-- Registration Number Display Box -->
                        <div class="registration-number-box mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                                    <i class="bi bi-hash text-primary"></i> Nomor Pendaftaran
                                </span>
                                <button type="button" class="copy-pill-btn" onclick="salinNomorPendaftaran('{{ $pendaftar->no_pendaftaran }}')">
                                    <i class="bi bi-clipboard"></i>
                                    <span id="copyBtnText">Salin Nomor</span>
                                </button>
                            </div>
                            <div class="reg-number-code py-1">
                                {{ $pendaftar->no_pendaftaran }}
                            </div>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1 text-primary"></i>Harap screenshot atau catat nomor unik ini
                            </div>
                        </div>

                        <!-- Candidate Data Summary Card -->
                        <div class="details-wrapper mb-4">
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="bi bi-person"></i>
                                    <span>Nama</span>
                                </div>
                                <div class="detail-value">
                                    {{ $pendaftar->nama_lengkap }}
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="bi bi-briefcase"></i>
                                    <span>Jurusan</span>
                                </div>
                                <div class="detail-value">
                                    {{ $pendaftar->jurusan->nama ?? '-' }}
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Status</span>
                                </div>
                                <div>
                                    <div class="status-badge-modern">
                                        <span class="status-dot-pulse"></span>
                                        <span>Menunggu Verifikasi</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Steps / Guidance Info -->
                        <div class="info-step-banner mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-lightbulb text-primary fs-5 mt-n1"></i>
                                <div class="small text-secondary">
                                    <strong class="text-dark d-block mb-1">Langkah Selanjutnya:</strong>
                                    Panitia SPMB akan memverifikasi berkas Anda dalam 1x24 jam kerja. Anda dapat memantau status secara berkala.
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Group -->
                        <div class="d-flex flex-column gap-2">
                            <!-- Main Primary Action (Preserved Original URL & Query) -->
                            <a href="{{ route('spmb.public.index') }}?cari={{ $pendaftar->no_pendaftaran }}"
                                class="btn-ocean-primary w-100">
                                <i class="bi bi-search"></i>
                                <span>Cek Status Sekarang</span>
                            </a>

                            <!-- Secondary Utility Actions -->
                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <button type="button" onclick="window.print()" class="btn-secondary-action w-100">
                                        <i class="bi bi-printer"></i>
                                        <span>Cetak Bukti</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('spmb.public.index') }}" class="btn-secondary-action w-100">
                                        <i class="bi bi-house"></i>
                                        <span>Ke Beranda</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /SUCCESS CARD -->

                </div>
            </div>
        </div>
    </main>

    <!-- =========================
        FOOTER
    ========================== -->
    <footer class="spmb-footer text-center">
        <div class="container">
            <div class="mb-1 fw-semibold text-secondary">SMK Yadika Soreang &bull; Sistem Penerimaan Siswa Baru</div>
            <div class="text-muted" style="font-size: 0.75rem;">Mencetak Generasi Berkarakter, Kompeten, dan Siap Kerja</div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Copy Helper Script -->
    <script>
        function salinNomorPendaftaran(nomor) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(nomor).then(function() {
                    const btnText = document.getElementById('copyBtnText');
                    btnText.innerText = 'Tersalin!';
                    setTimeout(() => {
                        btnText.innerText = 'Salin Nomor';
                    }, 2500);
                });
            } else {
                // Fallback for older browsers
                const tempInput = document.createElement('input');
                tempInput.value = nomor;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                const btnText = document.getElementById('copyBtnText');
                btnText.innerText = 'Tersalin!';
                setTimeout(() => {
                    btnText.innerText = 'Salin Nomor';
                }, 2500);
            }
        }
    </script>
</body>

</html>
