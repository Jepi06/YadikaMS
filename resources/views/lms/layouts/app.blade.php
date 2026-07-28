<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - LMS Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f7fb;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #0d47a1;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar .brand {
            color: #fff;
            font-weight: 700;
            padding: 1.25rem 1.25rem 1rem;
            display: block;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .8);
            padding: .65rem 1.25rem;
            font-size: .92rem;
            border-radius: 0;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .main-content {
            margin-left: 250px;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e9f0;
        }

        @media (max-width:991.98px) {
            .sidebar {
                left: -250px;
                transition: left .2s;
                z-index: 1040;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <aside class="sidebar" id="lmsSidebar">
        <a href="{{ route('lms') }}" class="brand"><i class="bi bi-mortarboard-fill me-1"></i> LMS Yadika</a>
        <nav class="nav flex-column mt-2">
            @if ($lmsUser?->isAdminLms())
                <a class="nav-link {{ request()->routeIs('lms.admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('lms.admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            @elseif ($lmsUser?->isGuruLms())
                <a class="nav-link {{ request()->routeIs('lms.guru.dashboard') ? 'active' : '' }}"
                    href="{{ route('lms.guru.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            @elseif ($lmsUser?->isSiswaLms())
                <a class="nav-link {{ request()->routeIs('lms.siswa.dashboard') ? 'active' : '' }}"
                    href="{{ route('lms.siswa.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('lms.siswa.presensi.riwayat') ? 'active' : '' }}"
                    href="{{ route('lms.siswa.presensi.riwayat') }}">
                    <i class="bi bi-calendar-check me-2"></i> Riwayat Presensi
                </a>
            @endif
        </nav>
    </aside>

    <div class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between px-3 py-2">
            <button class="btn btn-sm btn-outline-secondary d-lg-none"
                onclick="document.getElementById('lmsSidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="small text-muted">{{ $lmsUser?->name }} &middot; {{ $lmsUser?->role_lms_label }}</span>
                <form method="POST" action="{{ route('lms.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <main class="p-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>
