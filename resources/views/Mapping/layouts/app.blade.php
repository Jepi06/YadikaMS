<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem PKL') - SMKN Mapping PKL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --ocean-950: #0a192f;
            --ocean-900: #0c2340;
            --ocean-600: #1e62ad;
            --ocean-500: #257fdb;
            --sky-500: #38bdf8;
            --sky-400: #60a5fa;
            --sky-200: #bae6fd;
            --bg-page: #f4f8fc;
            --border-subtle: #e2eaf4;
        }

        body {
            background: var(--bg-page);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #0f233a;
            -webkit-font-smoothing: antialiased;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(185deg, var(--ocean-950) 0%, var(--ocean-900) 60%, #0d2847 100%);
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transition: transform .25s ease;
        }

        .sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .sidebar .brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: .95rem;
        }

        .sidebar .brand small {
            color: var(--sky-200);
            font-size: .75rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: .55rem 1.25rem;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(56, 189, 248, .16);
            color: #fff;
        }

        .sidebar .nav-link.active {
            box-shadow: inset 3px 0 0 var(--sky-500);
        }

        .sidebar .nav-link i {
            width: 22px;
        }

        .sidebar .nav-section {
            color: var(--sky-400);
            font-size: .7rem;
            font-weight: 700;
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
            border-bottom: 1px solid var(--border-subtle);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-content {
            padding: 1.5rem;
        }

        .card {
            border: 1px solid var(--border-subtle);
            box-shadow: 0 2px 8px rgba(12, 35, 64, .05);
            border-radius: 14px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border-subtle);
            font-weight: 600;
            border-radius: 14px 14px 0 0 !important;
        }

        .table th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ocean-900);
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--ocean-600), var(--ocean-500));
            border-color: var(--ocean-600);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--ocean-500), var(--sky-500));
            border-color: var(--ocean-500);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--sky-400);
            box-shadow: 0 0 0 .2rem rgba(56, 189, 248, .2);
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
            background: #f0f9ff;
            border-color: var(--ocean-500);
        }

        .siswa-checkbox-item.selected {
            background: #e0f2fe;
            border-color: var(--ocean-500);
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

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: inline-block;
            }

            .page-content {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php($pklUser = auth('pkl')->user())

    {{-- Sidebar --}}
    <nav class="sidebar" id="sidebarPkl">
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

            @guest('pkl')
                {{-- Pengunjung publik (belum login) --}}
                <a href="{{ route('pkl.pengajuan.create') }}"
                    class="nav-link {{ request()->routeIs('pkl.pengajuan.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-plus me-2"></i>Ajukan Tempat PKL
                </a>
               
                <a href="{{ route('pkl.login') }}" class="nav-link {{ request()->routeIs('pkl.login') ? 'active' : '' }}">
                    <i class="bi bi-lock me-2"></i>Login Sistem PKL
                </a>
            @endguest

            @auth('pkl')
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

                @if ($pklUser->isAdminAtauHubin())
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
                @endif

                @if ($pklUser->isApproverPkl())
                    <div class="nav-section">Persetujuan</div>
                    <a href="{{ route('approval.index') }}"
                        class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-circle me-2"></i>Approval
                    </a>
                @endif

                <div class="nav-section">Akun</div>
                <a href="{{ route('pkl.profil.edit') }}"
                    class="nav-link {{ request()->routeIs('pkl.profil.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle me-2"></i>Profil Saya
                </a>
            @endauth
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary sidebar-toggle"
                    onclick="document.getElementById('sidebarPkl').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h6 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h6>
            </div>

            @auth('pkl')
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('pkl.profil.edit') }}" class="d-flex align-items-center text-decoration-none">
                        @if ($pklUser?->avatar)
                            <img src="{{ Storage::url($pklUser->avatar) }}" class="rounded-circle me-2"
                                style="width:28px;height:28px;object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2 fw-bold"
                                style="width:28px;height:28px;font-size:.75rem">
                                {{ strtoupper(substr($pklUser?->name ?? '?', 0, 1)) }}
                            </div>
                        @endif
                        <span class="badge bg-primary-subtle text-primary fw-normal px-3 py-2">
                            {{ $pklUser?->role_pkl_label }} — {{ $pklUser?->name }}
                        </span>
                    </a>
                    <form action="{{ route('pkl.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </div>
            @endauth

            @guest('pkl')
                <a href="{{ route('pkl.login') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
            @endguest
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
