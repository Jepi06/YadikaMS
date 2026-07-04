<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem PKL') - SMKN Mapping PKL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a3c6e 0%, #2563eb 100%);
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            padding-top: 0;
        }

        .sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, .15);
        }

        .sidebar .brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: .95rem;
        }

        .sidebar .brand small {
            color: rgba(255, 255, 255, .6);
            font-size: .78rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: .55rem 1.25rem;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: .875rem;
            transition: all .2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .sidebar .nav-link i {
            width: 22px;
        }

        .sidebar .nav-section {
            color: rgba(255, 255, 255, .4);
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .75rem 1.25rem .25rem;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-content {
            padding: 1.5rem;
        }

        .card {
            border: 0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            border-radius: 12px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        .stat-card {
            border-radius: 12px;
        }

        .badge-step {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 600;
        }

        .table th {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 600;
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
        }

        .siswa-checkbox-item {
            padding: .5rem .75rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: .4rem;
            cursor: pointer;
            transition: all .15s;
        }

        .siswa-checkbox-item:hover {
            background: #f8f9ff;
            border-color: #2563eb;
        }

        .siswa-checkbox-item.selected {
            background: #eff6ff;
            border-color: #2563eb;
        }

        .siswa-checkbox-item.disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .approval-step {
            position: relative;
        }

        .approval-step::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -20px;
            width: 40px;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }

        .approval-step:last-child::after {
            display: none;
        }
    </style>
</head>

<body>

    {{--
    PERBAIKAN PENTING: seluruh sistem PKL pakai guard 'pkl' (lihat AuthenticatePkl,
    RoleMiddleware, AuthController — semua Auth::guard('pkl')). Direktif @auth /
    auth()->user() tanpa parameter memeriksa guard DEFAULT ('web'), bukan guard
    'pkl'. Kalau dibiarkan, sidebar/topbar bisa salah anggap "belum login"
    walau user sudah login di sistem PKL. Maka di bawah ini semua dipaksa
    pakai guard('pkl') secara eksplisit.
--}}
    @php($pklUser = auth('pkl')->user())

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <h5><i class="bi bi-mortarboard-fill me-2"></i>Mapping PKL</h5>
            <small>SMK Yadika Soreang — Sistem Pengelolaan PKL</small>
        </div>
        <div class="py-2">
            <div class="nav-section">Utama</div>
            <a href="{{ route('pkl.dashboard') }}"
                class="nav-link {{ request()->routeIs('pkl.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>

            @auth('pkl')
                {{-- Menu khusus akun SISWA (role_pkl = 'siswa') --}}
                @if ($pklUser->isSiswa())
                    <div class="nav-section">PKL Saya</div>
                    <a href="{{ route('siswa.pkl.status') }}"
                        class="nav-link {{ request()->routeIs('siswa.pkl.status') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i>Status Pengajuan Saya
                    </a>
                    <a href="{{ route('siswa.pkl.create') }}"
                        class="nav-link {{ request()->routeIs('siswa.pkl.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle me-2"></i>Ajukan Tempat PKL
                    </a>
                @endif

                {{-- Menu Data Master & Penempatan: disembunyikan dari akun siswa,
             karena siswa hanya perlu menu "PKL Saya" di atas --}}
                @if (!$pklUser->isSiswa())
                    <div class="nav-section">Data Master</div>
                    <a href="{{ route('siswa.index') }}"
                        class="nav-link {{ request()->routeIs('siswa.index') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>Data Siswa
                    </a>
                    <a href="{{ route('siswa.belum-mengajukan') }}"
                        class="nav-link {{ request()->routeIs('siswa.belum-mengajukan') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-circle me-2"></i>Belum Mengajukan PKL
                    </a>
                    <a href="{{ route('guru.index') }}"
                        class="nav-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge me-2"></i>Guru Pembimbing
                    </a>
                    <a href="{{ route('tempat.index') }}"
                        class="nav-link {{ request()->routeIs('tempat.*') ? 'active' : '' }}">
                        <i class="bi bi-building me-2"></i>Tempat PKL
                    </a>

                    <div class="nav-section">PKL</div>
                    <a href="{{ route('penempatan.index') }}"
                        class="nav-link {{ request()->routeIs('penempatan.*') ? 'active' : '' }}">
                        <i class="bi bi-map me-2"></i>Penempatan PKL
                    </a>

                    {{-- PERBAIKAN: sebelumnya ditampilkan untuk "role !== admin" (memakai
             kolom role yang salah), sehingga siapapun yang bukan admin —
             termasuk nantinya siswa — ikut melihat menu Approval. Sekarang
             dibatasi eksplisit hanya untuk 4 role approver PKL. --}}
                    @if ($pklUser->isApproverPkl())
                        <a href="{{ route('approval.index') }}"
                            class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                            <i class="bi bi-check2-circle me-2"></i>Approval
                        </a>
                    @endif
                @endif
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div>
                <h6 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h6>
            </div>
            <div class="d-flex align-items-center gap-3">
                @auth('pkl')
                    {{-- PERBAIKAN: getRoleLabel() tidak ada -> role_pkl_label (accessor asli) --}}
                    <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">
                        {{ $pklUser->role_pkl_label }} — {{ $pklUser->name }}
                    </span>
                    {{-- PERBAIKAN: route('logout') tidak terdaftar, nama route yang benar
                 adalah 'pkl.logout' (lihat web.php) --}}
                    <form action="{{ route('pkl.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        <div class="page-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
