<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk LMS - SMK Yadika Soreang</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/authLms.css') }}">
</head>
<body>

    <div class="login-card">

        <!-- ==============================================
             KOLOM KIRI: BRANDING (TAMPIL DI DESKTOP/TABLET)
             ============================================== -->
        <div class="branding-sidebar">
            <div>
                <div class="brand-header">
                    <div class="brand-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="brand-meta">
                        <div class="title">SMK YADIKA</div>
                        <div class="sub">Soreang • LMS Digital</div>
                    </div>
                </div>

                <div class="portal-badge">
                    <span class="badge-dot"></span>
                    <span>Portal Pembelajaran Resmi</span>
                </div>

                <h2 class="branding-title">Sistem Pembelajaran Digital SMK Yadika Soreang</h2>
                <p class="branding-desc">
                    Presensi, materi, tugas, dan rekap nilai dalam satu portal terpadu untuk siswa, guru, dan wali kelas.
                </p>

                <div class="features-list">
                    <div class="feature-card-item">
                        <div class="feature-icon-box">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <div class="feature-text">
                            <span class="item-title">Presensi QR Dinamis</span>
                            <span class="item-sub">Pencatatan kehadiran harian per mata pelajaran</span>
                        </div>
                    </div>

                    <div class="feature-card-item">
                        <div class="feature-icon-box">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div class="feature-text">
                            <span class="item-title">Materi &amp; Tugas Interaktif</span>
                            <span class="item-sub">Akses materi, kumpulkan tugas, pantau progres</span>
                        </div>
                    </div>

                    <div class="feature-card-item">
                        <div class="feature-icon-box">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="feature-text">
                            <span class="item-title">Rekap Nilai Real-time</span>
                            <span class="item-sub">Transparansi capaian nilai dan absensi siswa</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="branding-foot">
                <span>© {{ date('Y') }} SMK Yadika Soreang</span>
                <span>T.A 2026/2027</span>
            </div>
        </div>

        <!-- ==============================================
             KOLOM KANAN: FORMULIR LOGIN PENGGUNA
             ============================================== -->
        <div class="form-section">

            <!-- Compact Header (Hanya Muncul di Tampilan Layar Smartphone) -->
            <div class="mobile-brand-bar">
                <div class="mobile-brand-left">
                    <div class="mobile-avatar">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <div class="mobile-title">SMK Yadika Soreang</div>
                        <div class="mobile-sub">Portal Pembelajaran LMS</div>
                    </div>
                </div>
                <span class="badge-pill">T.A 2026/2027</span>
            </div>

            <div class="top-action-bar">
                <a href="{{ route('lms') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                <span class="badge-pill">Akses Pengguna</span>
            </div>

            <div class="form-heading">
                <h1>Masuk ke LMS</h1>
                <p>Silakan masuk dengan akun terdaftar Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle" style="font-size: 1.1rem; flex-shrink: 0; margin-top: 1px;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('lms.login.process') }}" id="loginForm" class="auth-form">
                @csrf

                <!-- Input Email atau NIS -->
                <div class="input-group">
                    <label for="login" class="field-label">Email / NIS</label>
                    <div class="field-box">
                        <i class="bi bi-person-badge field-icon"></i>
                        <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                            placeholder="nama@smk.sch.id atau NIS" class="custom-input">
                    </div>
                    <p class="field-helper">Siswa bisa login pakai NIS, tidak wajib pakai email.</p>
                </div>

                <!-- Input Password -->
                <div class="input-group">
                    <label for="password" class="field-label">Password</label>
                    <div class="field-box">
                        <i class="bi bi-lock field-icon"></i>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••" class="custom-input" style="padding-right: 44px;">
                        <button type="button" id="togglePassword" class="password-toggle-btn" aria-label="Tampilkan atau sembunyikan password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options-row">
                    <label for="remember" class="remember-wrap">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <span class="support-link-text">Lupa password? Hubungi TU sekolah.</span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" id="submitBtn" class="btn-submit-action">
                    <span id="btnLabel">Masuk ke LMS</span>
                    <i class="bi bi-arrow-right" id="btnIcon"></i>
                </button>
            </form>

            <div class="mobile-footer-text">
                © {{ date('Y') }} SMK Yadika Soreang. Hak Cipta Dilindungi.
            </div>

        </div>

    </div>

    <!-- Script Interaktif Vanilla JS -->
    <script>
        (function() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnLabel = document.getElementById('btnLabel');
            const btnIcon = document.getElementById('btnIcon');

            // Fitur intip password
            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            // Animasi loading tombol submit
            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('btn-disabled');
                    if (btnLabel) btnLabel.textContent = 'Memverifikasi...';
                    if (btnIcon) btnIcon.className = 'bi bi-arrow-repeat';
                });
            }
        })();
    </script>
</body>
</html>
