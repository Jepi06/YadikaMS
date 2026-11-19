<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status SPMB - SMK Yadika Soreang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

     <link href="/css/SPMBDashboard.css" rel="stylesheet">
</head>

<body>

    {{-- ========== NAVBAR ========== --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <span class="brand-badge text-white shadow-sm">🎓</span>
                <span class="d-flex flex-column">
                    <span class="fs-5 lh-1 text-white fw-bold">SPMB SMK Yadika Soreang</span>
                    <span class="text-white-50 fw-medium" style="font-size: .72rem;">Portal Penerimaan Siswa Baru</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('spmb.pengajuan.create') }}"
                            class="btn btn-register d-inline-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square"></i> Daftar Sekarang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('spmb.login') }}" class="btn btn-admin d-inline-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- ========== HERO ========== --}}
    <header class="hero-banner">
        <div class="container text-center">
            <div class="mb-3">
                <span class="pill-badge">
                    <i class="bi bi-calendar2-check"></i>
                    Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}
                </span>
            </div>

            <h1 class="fw-bold text-dark mb-3 display-5 hero-title">
                Cek Status <span class="text-gradient">Pendaftaran</span>
            </h1>

            <p class="text-secondary mx-auto mb-0" style="font-size: 1.05rem; max-width: 580px;">
                Pantau proses verifikasi administrasi dan keputusan kelulusan calon peserta didik baru
                SMK Yadika Soreang secara real-time.
            </p>
        </div>
    </header>

    {{-- ========== CONTENT ========== --}}
    <main class="page-wrapper">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>

        <div class="container py-4 position-relative" style="z-index: 1;">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    {{-- FORM PENCARIAN --}}
                    <div class="card search-card mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center gap-2 mb-3 text-secondary fw-semibold"
                                style="font-size: .875rem;">
                                <i class="bi bi-info-circle-fill" style="color: var(--ocean);"></i>
                                Masukkan Nomor Pendaftaran (misal: YDK-2025-001) atau Nama Lengkap
                            </div>

                            <form method="GET">
                                <div class="input-group">
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-search search-inner-icon"></i>
                                        <input type="text" name="cari" value="{{ $keyword }}"
                                            class="form-control search-input" autocomplete="off"
                                            placeholder="Contoh: YDK-2025-001 atau Muhammad Rizki">
                                    </div>
                                    <button type="submit" class="btn btn-search">
                                        <span>Cari Data</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- HASIL PENCARIAN --}}
                    @if ($keyword != '')
                        <div class="card result-card mb-4">
                            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-person text-primary"></i> Hasil Pencarian
                                </span>
                                <span class="badge bg-light text-secondary border">
                                    Kata kunci: "{{ $keyword }}"
                                </span>
                            </div>

                            <div class="card-body p-0">
                                @forelse($hasil as $p)
                                    @if ($loop->first)
                                        <div class="result-list">
                                    @endif

                                    <div class="result-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="student-avatar">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $p->nama_lengkap }}</h6>
                                                <div class="d-flex align-items-center flex-wrap gap-2 text-muted small">
                                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                                        <i class="bi bi-hash text-muted"></i>{{ $p->no_pendaftaran }}
                                                    </span>
                                                    <span>•</span>
                                                    <span class="text-primary fw-medium d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-mortarboard"></i>
                                                        {{ optional($p->jurusan)->nama }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            @if ($p->status == 'diterima')
                                                <span class="badge-status badge-ok">
                                                    <span class="status-dot"></span>
                                                    <i class="bi bi-check-circle-fill"></i> Diterima
                                                </span>
                                            @elseif ($p->status == 'ditolak')
                                                <span class="badge-status badge-no">
                                                    <span class="status-dot"></span>
                                                    <i class="bi bi-x-circle-fill"></i> Tidak Diterima
                                                </span>
                                            @else
                                                <span class="badge-status badge-wait">
                                                    <span class="status-dot"></span>
                                                    <i class="bi bi-clock-history"></i> Menunggu
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($loop->last)
                                        </div>
                                    @endif
                                @empty
                                    <div class="text-center py-5 px-4 text-muted">
                                        <div class="empty-icon"><i class="bi bi-search"></i></div>
                                        <h6 class="fw-bold text-dark mb-1">Data tidak ditemukan</h6>
                                        <p class="small text-secondary mb-0">
                                            Tidak ada calon peserta didik dengan kata kunci
                                            "<strong>{{ $keyword }}</strong>".<br class="d-none d-sm-inline">
                                            Pastikan penulisan nama atau nomor pendaftaran sudah benar.
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        {{-- STATE AWAL: TIPS PENCARIAN --}}
                        <div class="tips-card shadow-sm p-4 text-center">
                            <div class="row g-3 justify-content-center">
                                <div class="col-md-4">
                                    <div class="p-3 h-100">
                                        <div class="feature-icon-box"><i class="bi bi-card-text"></i></div>
                                        <h6 class="fw-bold mb-1 text-dark">Nomor Pendaftaran</h6>
                                        <small class="text-muted">Cek bukti pendaftaran yang Anda peroleh saat registrasi online/offline.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 h-100">
                                        <div class="feature-icon-box"><i class="bi bi-person-badge"></i></div>
                                        <h6 class="fw-bold mb-1 text-dark">Nama Lengkap</h6>
                                        <small class="text-muted">Ketik nama calon siswa sesuai kartu keluarga atau ijazah.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 h-100">
                                        <div class="feature-icon-box"><i class="bi bi-shield-check"></i></div>
                                        <h6 class="fw-bold mb-1 text-dark">Data Valid</h6>
                                        <small class="text-muted">Informasi terintegrasi langsung dengan database resmi panitia SPMB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- BANTUAN --}}
                    <div class="text-center mt-4">
                        <div class="help-pill">
                            <i class="bi bi-question-circle-fill" style="color: var(--ocean);"></i>
                            <span class="text-muted small">
                                Kendala verifikasi atau data belum muncul?
                                {{-- TODO: ganti "#" dengan link WhatsApp/kontak panitia --}}
                                <a href="#" class="text-decoration-none fw-semibold ms-1" style="color: var(--ocean);">
                                    Hubungi Panitia SPMB <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    {{-- ========== FOOTER ========== --}}
    <footer class="site-footer">
        <div class="container text-center">
            <p class="mb-0 fw-medium">
                © {{ now()->year }} SMK Yadika Soreang. Sistem Penerimaan Murid Baru.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>