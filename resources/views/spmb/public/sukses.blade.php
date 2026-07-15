<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-brand {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg,#0d2b4e,#12406f);">
        <div class="container">
            <a class="navbar-brand" href="{{ route('spmb.public.index') }}">SMK Yadika Soreang</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 text-center">
                    <div class="card-body py-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem;"></i>
                        <h4 class="fw-bold mt-3">Pendaftaran Berhasil Dikirim!</h4>
                        <p class="text-muted">Simpan nomor pendaftaran di bawah ini untuk mengecek status Anda.</p>

                        <div class="border rounded p-3 my-3 bg-light">
                            <div class="text-muted small">Nomor Pendaftaran</div>
                            <div class="fs-4 fw-bold text-primary">{{ $pendaftar->no_pendaftaran }}</div>
                        </div>

                        <p class="mb-4">
                            Status pendaftaran Anda saat ini: <span class="badge bg-warning text-dark">Menunggu</span>
                        </p>

                        <a href="{{ route('spmb.public.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> Lihat Daftar Status Pendaftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
