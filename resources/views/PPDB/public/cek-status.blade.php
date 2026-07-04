<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status PPDB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            min-height: 100vh;
        }

        .hero {
            padding: 60px 20px 40px;
            text-align: center;
            color: #fff;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 20px;
            padding: 6px 18px;
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .05em;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero p { opacity: .8; font-size: 1rem; }

        .search-card {
            background: #fff;
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 32px 80px rgba(0,0,0,.3);
            max-width: 640px;
            margin: 0 auto;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .btn-cek {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            transition: all .2s;
            width: 100%;
        }

        .btn-cek:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(30,64,175,.4);
        }

        /* Result Cards */
        .result-diterima {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 2px solid #6ee7b7;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
        }

        .result-pending {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 2px solid #fcd34d;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
        }

        .result-ditolak {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 2px solid #fca5a5;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
        }

        .result-icon { font-size: 3.5rem; margin-bottom: 12px; }

        .result-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .info-grid {
            background: rgba(255,255,255,.7);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            text-align: left;
        }

        .info-item { display: flex; gap: 10px; margin-bottom: 8px; align-items: flex-start; }
        .info-label { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; min-width: 120px; }
        .info-value { font-weight: 600; }

        .footer-links {
            text-align: center;
            padding: 30px 20px;
            color: rgba(255,255,255,.6);
            font-size: .83rem;
        }

        .footer-links a { color: rgba(255,255,255,.8); text-decoration: none; }
        .footer-links a:hover { color: #fff; }
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-badge">🎓 PPDB SMK Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    <h1>Cek Status<br>Penerimaan Siswa Baru</h1>
    <p class="mt-2">Masukkan nomor pendaftaran Anda untuk melihat hasil seleksi</p>
</div>

<div class="container pb-5">
    <div class="search-card">
        <form action="{{ route('ppdb.cek-status') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-700" style="color:#1e3a8a;">Nomor Pendaftaran</label>
                <input type="text"
                       name="no_pendaftaran"
                       class="form-control"
                       placeholder="Contoh: PPDB-2025-0001"
                       value="{{ request('no_pendaftaran') }}"
                       style="font-family: monospace; letter-spacing: .05em;">
            </div>
            <button type="submit" class="btn-cek">
                <i class="bi bi-search me-2"></i>Cek Status Pendaftaran
            </button>
        </form>

        {{-- Result --}}
        @if(request('no_pendaftaran'))
            <div class="mt-4">
                @if(!$pendaftar)
                    <div class="text-center p-4" style="background:#fef2f2;border-radius:16px;border:2px solid #fecaca;">
                        <div style="font-size:2.5rem;">❌</div>
                        <div class="fw-700 text-danger mt-2">Nomor Tidak Ditemukan</div>
                        <div class="text-muted mt-1" style="font-size:.87rem;">
                            Nomor pendaftaran <b>{{ request('no_pendaftaran') }}</b> tidak ada dalam sistem.<br>
                            Periksa kembali nomor yang Anda masukkan.
                        </div>
                    </div>
                @elseif($pendaftar->status === 'diterima')
                    <div class="result-diterima">
                        <div class="result-icon">🎉</div>
                        <div class="result-title" style="color:#065f46;">SELAMAT! Anda DITERIMA</div>
                        <p style="color:#059669;font-size:.9rem;">Anda telah resmi diterima sebagai calon siswa baru</p>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">No. Daftar</span>
                                <span class="info-value font-mono">{{ $pendaftar->no_pendaftaran }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nama</span>
                                <span class="info-value">{{ $pendaftar->nama_lengkap }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jurusan</span>
                                <span class="info-value">{{ $pendaftar->jurusan->nama ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Asal Sekolah</span>
                                <span class="info-value">{{ $pendaftar->asal_sekolah }}</span>
                            </div>
                        </div>

                        <div class="mt-3 p-3 rounded-3" style="background:rgba(6,95,70,.1);">
                            <small style="color:#065f46;font-weight:600;">
                                📌 Harap datang ke sekolah dengan membawa berkas lengkap untuk proses daftar ulang.
                            </small>
                        </div>
                    </div>

                @elseif($pendaftar->status === 'pending')
                    <div class="result-pending">
                        <div class="result-icon">⏳</div>
                        <div class="result-title" style="color:#92400e;">Sedang Diproses</div>
                        <p style="color:#b45309;font-size:.9rem;">Pendaftaran Anda sedang dalam proses verifikasi admin</p>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">No. Daftar</span>
                                <span class="info-value font-mono" style="font-family:monospace;">{{ $pendaftar->no_pendaftaran }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nama</span>
                                <span class="info-value">{{ $pendaftar->nama_lengkap }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Jurusan</span>
                                <span class="info-value">{{ $pendaftar->jurusan->nama ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tanggal Daftar</span>
                                <span class="info-value">{{ $pendaftar->created_at->isoFormat('D MMMM Y') }}</span>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="result-ditolak">
                        <div class="result-icon">😔</div>
                        <div class="result-title" style="color:#991b1b;">Mohon Maaf, Tidak Diterima</div>
                        <p style="color:#dc2626;font-size:.9rem;">Pendaftaran Anda tidak memenuhi persyaratan penerimaan</p>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">No. Daftar</span>
                                <span class="info-value font-mono" style="font-family:monospace;">{{ $pendaftar->no_pendaftaran }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nama</span>
                                <span class="info-value">{{ $pendaftar->nama_lengkap }}</span>
                            </div>
                            @if($pendaftar->catatan_admin)
                            <div class="info-item">
                                <span class="info-label">Catatan</span>
                                <span class="info-value">{{ $pendaftar->catatan_admin }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="footer-links">
    <a href="{{ route('ppdb.login') }}">🔒 Admin Login</a>
    &nbsp;·&nbsp;
    Sistem PPDB SMK &copy; {{ date('Y') }}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
