<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | PPDB SMK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 40%, #3b82f6 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 32px 80px rgba(0,0,0,.25);
        }

        .logo-circle {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 24px;
        }

        h4 { font-weight: 800; color: #1e3a8a; }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            font-size: .9rem;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            color: #fff;
            transition: all .2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(30,64,175,.35);
        }

        .input-group-text {
            background: #f8faff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px 0 0 12px;
            color: #64748b;
        }

        .input-group .form-control { border-radius: 0 12px 12px 0; }

        .alert-danger { border-radius: 12px; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo-circle">🎓</div>
    <h4 class="text-center mb-1">PPDB SMK</h4>
    <p class="text-center text-muted mb-4" style="font-size:.87rem;">Portal Admin Penerimaan Siswa Baru</p>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('ppdb.login.process') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-600 text-sm">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="admin@ppdb.sch.id"
                       value="{{ old('email') }}" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-600 text-sm">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted" for="remember" style="font-size:.87rem;">Ingat saya</label>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
        </button>
    </form>

    <p class="text-center text-muted mt-4 mb-0" style="font-size:.8rem;">
        Sistem PPDB &copy; {{ date('Y') }} — Hanya untuk Admin
    </p>
</div>
</body>
</html>
