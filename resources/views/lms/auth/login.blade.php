<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk LMS - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            min-height:100vh; display:flex; align-items:center;
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        }
        .login-card { border:none; border-radius:16px; }
        .brand-icon {
            width:64px; height:64px; border-radius:16px; background:#e3f2fd; color:#0d47a1;
            display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card shadow-lg p-4">
                    <div class="card-body">
                        <div class="brand-icon mb-3"><i class="bi bi-mortarboard-fill"></i></div>
                        <h4 class="text-center fw-bold mb-1">Masuk ke LMS</h4>
                        <p class="text-center text-muted mb-4">SMK Yadika Soreang</p>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('lms.login.process') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Ingat saya</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('lms') }}" class="text-decoration-none small text-muted">
                                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
