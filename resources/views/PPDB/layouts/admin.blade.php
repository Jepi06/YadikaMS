<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PPDB SMK') | Sistem PPDB</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary:      #1e40af;
            --primary-dark: #1e3a8a;
            --primary-light:#3b82f6;
            --accent:       #0ea5e9;
            --bg:           #f0f4ff;
            --sidebar-w:    260px;
            --card-radius:  16px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: #1e293b;
        }

        /* ── Sidebar ─────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(160deg, var(--primary-dark) 0%, var(--primary) 60%, var(--primary-light) 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(30,58,138,.25);
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 800;
            font-size: 1.05rem;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand span {
            font-size: .72rem;
            color: rgba(255,255,255,.7);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sidebar-logo {
            width: 42px; height: 42px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 12px;
            backdrop-filter: blur(6px);
        }

        .sidebar nav { padding: 16px 12px; flex: 1; }

        .nav-section-title {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255,255,255,.5);
            padding: 12px 8px 6px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .875rem;
            font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            transition: all .2s;
            margin-bottom: 2px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }

        .sidebar .nav-link.active { font-weight: 700; }
        .sidebar .nav-link i { font-size: 1.1rem; min-width: 20px; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.15);
        }

        .sidebar-footer .user-name {
            font-weight: 600;
            font-size: .875rem;
            color: #fff;
        }

        .sidebar-footer .user-role {
            font-size: .75rem;
            color: rgba(255,255,255,.6);
        }

        /* ── Main Content ────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }

        .page-title { font-weight: 700; font-size: 1.1rem; color: var(--primary-dark); }

        .content { padding: 28px; flex: 1; }

        /* ── Cards ───────────────────────────────── */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 12px rgba(30,58,138,.08);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e8edf5;
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
            padding: 16px 20px;
            font-weight: 700;
            font-size: .95rem;
            color: var(--primary-dark);
        }

        /* ── Stat Cards ──────────────────────────── */
        .stat-card {
            border-radius: var(--card-radius);
            padding: 22px 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            right: -20px; top: -20px;
            width: 100px; height: 100px;
            background: rgba(255,255,255,.1);
            border-radius: 50%;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: .85;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: .8rem;
            opacity: .85;
            font-weight: 500;
        }

        .stat-blue   { background: linear-gradient(135deg, #1e40af, #3b82f6); }
        .stat-green  { background: linear-gradient(135deg, #059669, #34d399); }
        .stat-yellow { background: linear-gradient(135deg, #d97706, #fbbf24); }
        .stat-red    { background: linear-gradient(135deg, #dc2626, #f87171); }

        /* ── Badges ──────────────────────────────── */
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
            display: inline-block;
        }

        /* ── Table ───────────────────────────────── */
        .table { font-size: .875rem; }
        .table th {
            background: #f8faff;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 2px solid #dbeafe;
        }
        .table td { vertical-align: middle; }

        /* ── Buttons ─────────────────────────────── */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-wa {
            background: #25d366;
            border-color: #25d366;
            color: #fff;
        }
        .btn-wa:hover {
            background: #128c7e;
            border-color: #128c7e;
            color: #fff;
        }

        /* ── Filter Bar ──────────────────────────── */
        .filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 8px rgba(30,58,138,.07);
        }

        /* ── Progress Bar ────────────────────────── */
        .progress { border-radius: 8px; height: 8px; }
        .progress-bar { background: linear-gradient(90deg, var(--primary), var(--accent)); }

        /* ── Alert ───────────────────────────────── */
        .alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .alert-danger  { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

        /* ── Jurusan Tabs ─────────────────────────── */
        .nav-tabs .nav-link {
            color: var(--primary);
            font-weight: 600;
            border-radius: 10px 10px 0 0;
        }
        .nav-tabs .nav-link.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }

        /* ── Mono font for codes ─────────────────── */
        .font-mono { font-family: 'DM Mono', monospace; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">🎓</div>
        <h5>PPDB SMK</h5>
        <span>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</span>
    </div>

    <nav>
        <div class="nav-section-title">Menu Utama</div>

        <a href="{{ route('ppdb.dashboard') }}" class="nav-link {{ request()->routeIs('ppdb.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section-title">Data Pendaftar</div>

        <a href="{{ route('ppdb.pendaftar.index') }}" class="nav-link {{ request()->routeIs('ppdb.pendaftar.index') && !request('jurusan') && !request('status') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Semua Pendaftar
        </a>

        @foreach(\App\Models\PPDB\Jurusan::all() as $j)
        <a href="{{ route('ppdb.pendaftar.index', ['jurusan' => $j->id]) }}"
           class="nav-link {{ request('jurusan') == $j->id ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i> {{ $j->kode }}
        </a>
        @endforeach

        <div class="nav-section-title">Status</div>

        <a href="{{ route('ppdb.pendaftar.index', ['status' => 'diterima']) }}"
           class="nav-link {{ request('status') === 'diterima' ? 'active' : '' }}">
            <i class="bi bi-check-circle"></i> Diterima
        </a>

        <a href="{{ route('ppdb.pendaftar.index', ['status' => 'pending']) }}"
           class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Menunggu
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;">
                👤
            </div>
            <div>
                <div class="user-name"> {{ Auth::guard('ppdb')->user()->name }}</div>
                <div class="user-role">ppdbistrator</div>
            </div>
        </div>
        <form action="{{ route('ppdb.logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:8px;">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="main-wrapper">
    <header class="topbar">
        <div class="page-title">@yield('page-title', 'Dashboard')</div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.75rem;padding:6px 12px;border-radius:20px;">
                <i class="bi bi-calendar3"></i> {{ now()->isoFormat('D MMMM Y') }}
            </span>
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
