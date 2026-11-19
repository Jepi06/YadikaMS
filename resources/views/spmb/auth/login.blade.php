<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin SPMB - SMK Yadika Soreang</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-navy: #082847;
            --ocean-deep: #0a3d68;
            --ocean-blue: #0284c7;
            --sky-blue: #38bdf8;
            --sky-light: #e0f2fe;
            --sky-glow: rgba(56, 189, 248, 0.25);
            --card-glass: rgba(255, 255, 255, 0.94);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-navy);
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.28) 0px, transparent 55%),
                radial-gradient(at 100% 100%, rgba(2, 132, 199, 0.35) 0px, transparent 60%),
                radial-gradient(at 50% 50%, rgba(8, 40, 71, 0.95) 0px, #041628 100%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
            padding: 1.5rem 1rem;
        }

        /* Ambient Glowing Geometric Orbs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }
        .orb-1 {
            top: 5%;
            left: 10%;
            width: 320px;
            height: 320px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
        }
        .orb-2 {
            bottom: 5%;
            right: 10%;
            width: 360px;
            height: 360px;
            background: linear-gradient(135deg, #0369a1, #0ea5e9);
        }

        /* Subtle grid background overlay */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        /* Glassmorphism Card with Gradient Border */
        .auth-card {
            background: var(--card-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 
                0 24px 50px -12px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(56, 189, 248, 0.15),
                0 1px 3px 0 rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Accent top gradient stripe */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #06b6d4 100%);
        }

        /* Logo Emblem Box */
        .brand-badge {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 2rem;
            box-shadow: 0 10px 20px -4px rgba(2, 132, 199, 0.45);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .brand-badge:hover {
            transform: scale(1.05) rotate(-2deg);
        }

        .portal-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--sky-light);
            color: #0369a1;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 50px;
            border: 1px solid rgba(56, 189, 248, 0.4);
            margin-bottom: 0.6rem;
        }

        .app-title {
            color: #0f172a;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
        }

        .app-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Input Styles */
        .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 4;
        }

        .form-control-modern {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.65rem;
            font-size: 0.925rem;
            font-weight: 500;
            color: #0f172a;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.85rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control-modern:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
            outline: none;
        }

        .form-control-modern:focus + .input-icon,
        .input-group-modern:focus-within .input-icon {
            color: #0284c7;
        }

        /* Password toggle button */
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.05rem;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            z-index: 4;
        }

        .password-toggle-btn:hover {
            color: #0284c7;
        }

        /* Checkbox Customization */
        .custom-check .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            margin-top: 0.15rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .custom-check .form-check-input:checked {
            background-color: #0284c7;
            border-color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
        }

        .custom-check .form-check-label {
            font-size: 0.85rem;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
            margin-left: 0.25rem;
        }

        /* Primary Submit Button */
        .btn-ocean-submit {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            border: none;
            border-radius: 0.85rem;
            padding: 0.825rem 1.25rem;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 20px -4px rgba(2, 132, 199, 0.4);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-ocean-submit:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -4px rgba(2, 132, 199, 0.5);
        }

        .btn-ocean-submit:active {
            transform: translateY(0);
        }

        /* Back Link */
        .back-link {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link:hover {
            color: #38bdf8;
            transform: translateX(-3px);
        }

        /* Error Alert Modern */
        .alert-modern-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 0.85rem;
            padding: 0.75rem 1rem;
            color: #991b1b;
        }

        /* Security Assurance pill */
        .security-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* Bottom copyright */
        .footer-note {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>

    <!-- Ambient glowing backgrounds -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="grid-pattern"></div>

    <div class="login-container">
        <!-- Main Card -->
        <div class="auth-card p-4 p-md-5">
            <!-- Brand & Header -->
            <div class="text-center mb-4">
                <div class="brand-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <span class="portal-pill">
                        <i class="bi bi-shield-lock-fill"></i> Portal Administrator
                    </span>
                </div>
                <h4 class="app-title mb-1">Login Admin SPMB</h4>
                <div class="app-subtitle">SMK Yadika Soreang</div>
            </div>

            <!-- Error Notifications (Laravel Blade Preserved) -->
            @if ($errors->any())
                <div class="alert alert-modern-danger mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0 mt-1"></i>
                        <div class="fw-semibold small">Gagal Masuk:</div>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Process (Laravel Blade Preserved) -->
            <form method="POST" action="{{ route('spmb.login.process') }}">
                @csrf

                <!-- Input Email -->
                <div class="mb-3">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-group-modern">
                        <input type="email" 
                               id="email"
                               name="email" 
                               class="form-control-modern" 
                               placeholder="admin@smkyadikasoreang.sch.id"
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        <i class="bi bi-envelope-at input-icon"></i>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0" for="password">Kata Sandi</label>
                    </div>
                    <div class="input-group-modern">
                        <input type="password" 
                               id="password"
                               name="password" 
                               class="form-control-modern pe-5" 
                               placeholder="••••••••••••"
                               required>
                        <i class="bi bi-key input-icon"></i>
                        <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Lihat Password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check custom-check mb-0">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <div class="security-note">
                        <i class="bi bi-shield-check text-success"></i> Terenkripsi SSL
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-ocean-submit w-100 mb-3">
                    <span>Masuk ke Dashboard</span>
                    <i class="bi bi-arrow-right-short fs-5"></i>
                </button>
            </form>

            <!-- Back to Public Link -->
            <div class="text-center pt-2 border-top">
                <a href="{{ route('spmb.public.index') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke halaman cek status</span>
                </a>
            </div>
        </div>

        <!-- Footer Copyright -->
        <div class="footer-note">
            &copy; {{ now()->year }} SMK Yadika Soreang &bull; Sistem Penerimaan Murid Baru
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Toggle Visibility Password -->
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput && toggleIcon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            }
        });
    </script>
</body>

</html>
