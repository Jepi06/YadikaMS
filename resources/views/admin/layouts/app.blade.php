<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Super Admin') - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f4f4f6; }
        .sidebar {
            width: 250px; min-height: 100vh; background: #1f2937;
            position: fixed; top: 0; left: 0;
        }
        .sidebar .brand { color: #fff; font-weight: 700; padding: 1.25rem; display: block; }
        .sidebar .brand small { display: block; font-weight: 400; color: #9ca3af; font-size: .72rem; }
        .sidebar .nav-link { color: #d1d5db; padding: .65rem 1.25rem; font-size: .92rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: rgba(255,255,255,.08); color: #fff; }
        .main-content { margin-left: 250px; }
        .topbar { background: #fff; border-bottom: 1px solid #e5e7eb; }
        .badge-module { font-size: .7rem; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <i class="bi bi-shield-lock-fill me-1"></i> Super Admin
            <small>Lintas Modul: PKL · SPMB · LMS</small>
        </a>
        <nav class="nav flex-column mt-2">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}" href="{{ route('admin.pengguna.index') }}">
                <i class="bi bi-people-fill me-2"></i> Kelola Pengguna
            </a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between px-3 py-2">
            <span class="small text-muted">Panel Super Admin</span>
            <span class="small text-muted">
                {{ request()->attributes->get('superAdminUser')?->name }}
                <span class="badge bg-dark ms-1">Super Admin</span>
            </span>
        </div>

        <main class="p-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

</body>
</html>
