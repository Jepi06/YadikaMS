<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Informasi PKL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      min-height: 100vh;
      background: radial-gradient(circle at top left, #1e3a8a 0%, #0c2340 35%, #071527 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
      color: #0f172a;
      position: relative;
      overflow-x: hidden;
    }

    .bg-glow-1 {
      position: absolute;
      top: -120px;
      right: -80px;
      width: 480px;
      height: 480px;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(14, 165, 233, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
      filter: blur(40px);
    }

    .bg-glow-2 {
      position: absolute;
      bottom: -150px;
      left: -100px;
      width: 520px;
      height: 520px;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(30, 58, 138, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
      filter: blur(50px);
    }

    .login-container {
      width: 100%;
      max-width: 960px;
      background: rgba(255, 255, 255, 0.98);
      border-radius: 28px;
      box-shadow: 0 25px 60px -15px rgba(3, 15, 38, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.1);
      overflow: hidden;
      display: grid;
      grid-template-columns: 1.05fr 1fr;
      position: relative;
      z-index: 10;
    }

    .info-panel {
      background: linear-gradient(145deg, #091a33 0%, #0d284f 55%, #0369a1 100%);
      color: #ffffff;
      padding: 48px 40px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
    }

    .info-panel::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 85% 20%, rgba(56, 189, 248, 0.25) 0%, transparent 60%);
      pointer-events: none;
    }

    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .brand-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 16px rgba(2, 132, 199, 0.35);
    }

    .brand-icon svg {
      width: 24px;
      height: 24px;
      color: #ffffff;
    }

    .brand-title {
      font-size: 19px;
      font-weight: 800;
      letter-spacing: -0.02em;
      color: #ffffff;
      line-height: 1.2;
    }

    .brand-subtitle {
      font-size: 12px;
      font-weight: 500;
      color: #93c5fd;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      margin: 32px 0;
    }

    .hero-content h2 {
      font-size: 26px;
      font-weight: 800;
      line-height: 1.3;
      letter-spacing: -0.02em;
      color: #ffffff;
      margin-bottom: 12px;
    }

    .hero-content h2 span {
      background: linear-gradient(90deg, #7dd3fc, #bae6fd);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-content p {
      font-size: 14px;
      line-height: 1.6;
      color: #cbd5e1;
      margin-bottom: 28px;
    }

    .features-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.12);
      padding: 10px 14px;
      border-radius: 12px;
      backdrop-filter: blur(8px);
    }

    .feature-item svg {
      width: 18px;
      height: 18px;
      color: #38bdf8;
      flex-shrink: 0;
    }

    .feature-item span {
      font-size: 13px;
      font-weight: 500;
      color: #e2e8f0;
    }

    .panel-footer {
      font-size: 12px;
      color: #94a3b8;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 20px;
    }

    .status-badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 600;
      color: #38bdf8;
      background: rgba(56, 189, 248, 0.12);
      padding: 4px 10px;
      border-radius: 9999px;
    }

    .status-dot {
      width: 7px;
      height: 7px;
      background: #38bdf8;
      border-radius: 50%;
      box-shadow: 0 0 8px #38bdf8;
    }

    .form-panel {
      padding: 48px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #ffffff;
    }

    .form-header {
      margin-bottom: 28px;
    }

    .form-tag {
      display: inline-block;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #0284c7;
      background: #e0f2fe;
      padding: 4px 10px;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .form-header h1 {
      font-size: 26px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.02em;
      margin-bottom: 6px;
    }

    .form-header p {
      font-size: 14px;
      color: #64748b;
    }

    .alert {
      padding: 12px 16px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert svg {
      flex-shrink: 0;
    }

    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
    }

    .alert-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #16a34a;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 7px;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 14px;
      width: 18px;
      height: 18px;
      color: #94a3b8;
      pointer-events: none;
      transition: color 0.2s;
    }

    .form-control {
      width: 100%;
      height: 46px;
      padding: 0 16px 0 42px;
      font-size: 14px;
      border-radius: 12px;
      border: 1.5px solid #e2e8f0;
      background: #f8fafc;
      color: #0f172a;
      transition: all 0.2s ease;
      outline: none;
    }

    .form-control.has-toggle {
      padding-right: 44px;
    }

    .form-control.is-invalid {
      border-color: #fca5a5;
      background: #fef2f2;
    }

    .form-control:focus {
      background: #ffffff;
      border-color: #0284c7;
      box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }

    .input-wrapper:focus-within .input-icon {
      color: #0284c7;
    }

    .form-control::placeholder {
      color: #94a3b8;
    }

    .password-toggle-btn {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: #94a3b8;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .password-toggle-btn:hover {
      color: #475569;
    }

    .form-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: 13px;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      user-select: none;
      color: #475569;
    }

    .checkbox-container input[type="checkbox"] {
      width: 17px;
      height: 17px;
      accent-color: #0284c7;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn-submit {
      width: 100%;
      height: 48px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
      color: #ffffff;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.01em;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
      transition: all 0.25s ease;
    }

    .btn-submit:hover {
      background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
      box-shadow: 0 12px 24px -5px rgba(2, 132, 199, 0.55);
      transform: translateY(-1px);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .quick-nav {
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      font-size: 13px;
    }

    .quick-nav-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #64748b;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .quick-nav-link:hover {
      color: #0284c7;
    }

    .btn-ajukan-link {
      color: #0284c7;
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 12px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }

    .btn-ajukan-link:hover {
      background: #e0f2fe;
      border-color: #7dd3fc;
    }

    @media (max-width: 860px) {
      .login-container {
        grid-template-columns: 1fr;
        max-width: 480px;
      }

      .info-panel {
        display: none;
      }

      .form-panel {
        padding: 36px 28px;
      }
    }
  </style>
</head>
<body>

  <div class="bg-glow-1"></div>
  <div class="bg-glow-2"></div>

  <div class="login-container">

    {{-- PANEL KIRI: Brand --}}
    <div class="info-panel">
      <div>
        <div class="brand-badge">
          <div class="brand-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
          </div>
          <div>
            <div class="brand-title">Monitoring PKL</div>
            <div class="brand-subtitle">Sistem Informasi Magang</div>
          </div>
        </div>

        <div class="hero-content">
          <h2>Portal Terpadu <span>Praktek Kerja Lapangan</span></h2>
          <p>
            Kelola persetujuan bertingkat dari Wali Kelas, Guru BK, Kesiswaan, hingga Kepala Jurusan dalam satu pintu monitoring terintegrasi.
          </p>

          <div class="features-list">
            <div class="feature-item">
              <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Verifikasi Multi-Tahap Cepat &amp; Transparan</span>
            </div>
            <div class="feature-item">
              <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span>Sinkronisasi Data Mitra Industri Aktif</span>
            </div>
            <div class="feature-item">
              <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
              <span>Keamanan Hak Akses Guru, Siswa &amp; Pembimbing</span>
            </div>
          </div>
        </div>
      </div>

      <div class="panel-footer">
        <span>Tahun Ajaran 2024/2025</span>
        <div class="status-badge-pill">
          <div class="status-dot"></div>
          Sistem Online
        </div>
      </div>
    </div>

    {{-- PANEL KANAN: Form Login --}}
    <div class="form-panel">
      <div class="form-header">
        <span class="form-tag">Akses Pengguna</span>
        <h1>Selamat Datang</h1>
        <p>Silakan masuk menggunakan kredensial akun terdaftar Anda.</p>
      </div>

      {{-- Pesan error validasi --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      {{-- Pesan error dari session --}}
      @if (session('error'))
        <div class="alert alert-danger">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      {{-- Pesan sukses dari session --}}
      @if (session('success'))
        <div class="alert alert-success">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <form action="{{ route('pkl.login.process') }}" method="POST">
        @csrf

        {{-- Email --}}
        <div class="form-group">
          <label class="form-label" for="email">Alamat Email</label>
          <div class="input-wrapper">
            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
            <input
              type="email"
              name="email"
              id="email"
              value="{{ old('email') }}"
              placeholder="nama@sekolah.sch.id"
              class="form-control @error('email') is-invalid @enderror"
              autocomplete="email"
              required
              autofocus
            >
          </div>
        </div>

        {{-- Password --}}
        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <div class="input-wrapper">
            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="••••••••••••"
              class="form-control has-toggle @error('password') is-invalid @enderror"
              autocomplete="current-password"
              required
            >
            <button type="button" class="password-toggle-btn" title="Lihat password" aria-label="Tampilkan atau sembunyikan password" onclick="togglePassword()">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </button>
          </div>
        </div>

        {{-- Ingat saya --}}
        <div class="form-meta">
          <label class="checkbox-container" for="remember">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Ingat saya</span>
          </label>
        </div>

        {{-- Tombol login --}}
        <button type="submit" class="btn-submit">
          <span>Masuk ke Sistem PKL</span>
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
          </svg>
        </button>
      </form>

      {{-- Navigasi cepat --}}
      <div class="quick-nav">
        <a href="/" class="quick-nav-link">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Dasbor Monitoring
        </a>

        <a href="{{ route('pkl.pengajuan.create') }}" class="btn-ajukan-link">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Form Ajukan PKL
        </a>
      </div>

    </div>
  </div>

  <script>
    function togglePassword() {
      const pwd = document.getElementById('password');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>