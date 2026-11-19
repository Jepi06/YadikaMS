<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel SPMB') - SMK Yadika Soreang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --ocean: #0284c7;
            --ocean-dark: #0369a1;
            --sky: #38bdf8;
        }

        /* ---------- Base ---------- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #0c2340 0%, #08192d 100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, .15);
            transition: margin .3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: 1.35rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .brand-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #fff;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(2, 132, 199, .4);
        }

        .sidebar-scroll {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem .85rem;
        }

        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .15);
            border-radius: 6px;
        }

        .section-label {
            padding: 1rem .85rem .4rem;
            color: #64748b;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sidebar a {
            color: #94a3b8;
            text-decoration: none;
        }

        .sidebar .nav-link-custom {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .3rem;
            padding: .7rem 1rem;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 600;
            transition: all .25s ease;
        }

        .sidebar .nav-link-custom i {
            font-size: 1.1rem;
        }

        .sidebar .nav-link-custom:hover {
            color: #fff;
            background: rgba(255, 255, 255, .06);
            transform: translateX(3px);
        }

        .sidebar .nav-link-custom.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(2, 132, 199, .9), rgba(56, 189, 248, .8));
            box-shadow: 0 4px 15px rgba(2, 132, 199, .35);
        }

        .jurusan-row {
            display: flex;
            align-items: center;
            gap: .25rem;
            margin-bottom: .3rem;
        }

        .jurusan-row .nav-link-custom {
            flex-grow: 1;
            min-width: 0;
            margin-bottom: 0;
        }

        .jurusan-row .nav-link-custom .label {
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .badge-kuota {
            padding: .25rem .5rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, .1);
            color: #e2e8f0;
            font-family: 'JetBrains Mono', monospace;
            font-size: .72rem;
            font-weight: 600;
        }

        .nav-link-custom.active .badge-kuota {
            background: rgba(255, 255, 255, .25);
        }

        .btn-export-mini {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: background .2s ease;
        }

        .btn-export-mini:hover {
            background: rgba(16, 185, 129, .15);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, .08);
            background: rgba(0, 0, 0, .15);
        }

        .btn-logout {
            width: 100%;
            margin-top: .35rem;
            padding: .6rem 1rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, .25);
            background: rgba(255, 255, 255, .05);
            color: #e2e8f0;
            font-size: .88rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-logout:hover {
            color: #fff;
            background: rgba(239, 68, 68, .85);
            border-color: transparent;
        }

        /* ---------- Layout utama ---------- */
        .main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 99;
            padding: .7rem 2rem;
            background: rgba(255, 255, 255, .88);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, .9);
        }

        .avatar-initial {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #fff;
            font-size: .8rem;
            font-weight: 700;
        }

        .avatar-img {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            object-fit: cover;
        }

        .content-container {
            flex-grow: 1;
            padding: 2rem;
        }

        .sidebar-backdrop {
            display: none;
        }

        .alert {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 14px -4px rgba(0, 0, 0, .08);
        }

        /* ---------- Komponen dashboard ---------- */
        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 1.35rem 1.4rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, .04);
            transition: all .3s cubic-bezier(.16, 1, .3, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: #cbd5e1;
            box-shadow: 0 14px 28px -4px rgba(2, 132, 199, .12);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card.card-primary::after { background: linear-gradient(90deg, #0284c7, #38bdf8); }
        .stat-card.card-success::after { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card.card-warning::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .stat-card.card-danger::after  { background: linear-gradient(90deg, #ef4444, #f87171); }

        .stat-label { letter-spacing: .04em; }
        .stat-hint  { font-size: .78rem; }

        .stat-icon-wrapper {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 1.35rem;
        }

        .stat-icon-wrapper.sm {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 1rem;
        }

        .stat-icon-primary { background: #e0f2fe; color: #0284c7; }
        .stat-icon-success { background: #dcfce7; color: #10b981; }
        .stat-icon-warning { background: #fef3c7; color: #f59e0b; }
        .stat-icon-danger  { background: #fee2e2; color: #ef4444; }

        .table-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 4px 24px -2px rgba(0, 0, 0, .04);
        }

        .table-card-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.75rem;
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table { margin-bottom: 0; }

        .custom-table thead th {
            padding: 1rem 1.5rem;
            background-color: #f8fafc;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .custom-table tbody tr { transition: background-color .2s ease; }
        .custom-table tbody tr:hover { background-color: #f8fafc; }

        .custom-table tbody td {
            padding: 1.15rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: .92rem;
        }

        .badge-quota {
            padding: .35rem .75rem;
            border-radius: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: .85rem;
            font-weight: 600;
        }

        .badge-quota-neutral { background-color: #f1f5f9; color: #475569; }
        .badge-quota-primary { background-color: #e0f2fe; color: #0369a1; }
        .badge-quota-success { background-color: #dcfce7; color: #15803d; }
        .badge-quota-warning { background-color: #fef3c7; color: #b45309; }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem 1rem;
            border-radius: 10px;
            border: 1px solid #bae6fd;
            background: #f0f9ff;
            color: var(--ocean);
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn-view:hover {
            color: #fff;
            background: var(--ocean);
            border-color: var(--ocean);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, .25);
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 991.98px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { margin-left: 0; }
            .main-wrapper { margin-left: 0; }

            .top-navbar,
            .content-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .sidebar-backdrop.show {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 999;
                background: rgba(15, 23, 42, .45);
            }
        }

        /* ---------- Print ---------- */
        @media print {
            .sidebar,
            .top-navbar,
            .no-print { display: none !important; }

            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>

<body>

    {{-- ========== SIDEBAR ========== --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div>
                <div class="text-white fw-bold lh-1">SPMB</div>
                <div class="text-info small fw-medium mt-1" style="font-size: .72rem;">SMK Yadika Soreang</div>
            </div>
        </div>

        <div class="sidebar-scroll">
            <a href="{{ route('spmb.admin.dashboard') }}"
                class="nav-link-custom {{ request()->routeIs('spmb.admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('spmb.admin.pendaftar.index') }}"
                class="nav-link-custom {{ request()->routeIs('spmb.admin.pendaftar.index') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> <span>Semua Pendaftar</span>
            </a>

            <div class="section-label">Per Jurusan</div>
            @foreach ($sidebarJurusan as $j)
                <div class="jurusan-row">
                    <a href="{{ route('spmb.admin.pendaftar.per-jurusan', $j) }}"
                        class="nav-link-custom {{ request()->routeIs('spmb.admin.pendaftar.per-jurusan') && request()->route('jurusan')?->id === $j->id ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span class="label">{{ $j->nama }}</span>
                        <span class="badge-kuota">{{ $j->pendaftar_count }}</span>
                    </a>
                    <a href="{{ route('spmb.admin.export.excel.per-jurusan', $j) }}" class="btn-export-mini"
                        title="Export Excel {{ $j->nama }}">
                        <i class="bi bi-file-earmark-excel text-success"></i>
                    </a>
                </div>
            @endforeach

            <div class="section-label">Export</div>
            <a href="{{ route('spmb.admin.export.excel') }}" class="nav-link-custom">
                <i class="bi bi-file-earmark-excel"></i> <span>Export Excel</span>
            </a>
            <a href="{{ route('spmb.admin.export.pdf') }}" class="nav-link-custom">
                <i class="bi bi-file-earmark-pdf"></i> <span>Rekap PDF</span>
            </a>

            <div class="section-label">Akun</div>
            <a href="{{ route('spmb.admin.profil.edit') }}"
                class="nav-link-custom {{ request()->routeIs('spmb.admin.profil.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> <span>Profil Saya</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('spmb.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    {{-- ========== MAIN ========== --}}
    <div class="main-wrapper">

        {{-- Topbar --}}
        <div class="top-navbar d-flex align-items-center justify-content-between">
            <button class="btn btn-light d-lg-none border-0 text-secondary" id="sidebarToggle" type="button"
                aria-label="Toggle Navigation">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="ms-auto">
                @php $spmbUser = auth('spmb')->user(); @endphp
                <a href="{{ route('spmb.admin.profil.edit') }}" class="d-flex align-items-center text-decoration-none">
                    @if ($spmbUser?->avatar)
                        <img src="{{ Storage::url($spmbUser->avatar) }}" class="avatar-img me-2" alt="">
                    @else
                        <span class="avatar-initial me-2">
                            {{ strtoupper(substr($spmbUser?->name ?? '?', 0, 1)) }}
                        </span>
                    @endif
                    <span class="small fw-semibold text-dark">{{ $spmbUser?->name }}</span>
                </a>
            </div>
        </div>

        {{-- Content --}}
        <main class="content-container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!toggle || !sidebar) return;

            const setOpen = (open) => {
                sidebar.classList.toggle('show', open);
                backdrop.classList.toggle('show', open);
            };

            toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('show')));
            backdrop.addEventListener('click', () => setOpen(false));
        })();
    </script>
    @stack('scripts')
</body>

</html>