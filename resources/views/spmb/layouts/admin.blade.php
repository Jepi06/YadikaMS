<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel SPMB') - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #0d2b4e; color: #fff; }
        .sidebar a { color: #cfe0f2; text-decoration: none; display: block; padding: .55rem 1rem; border-radius: .375rem; }
        .sidebar a:hover, .sidebar a.active { background: #12406f; color: #fff; }
        .sidebar .section-label { text-transform: uppercase; font-size: .72rem; letter-spacing: .05em; color: #7fa8d3; padding: 1rem 1rem .25rem; }
        .badge-kuota { font-size: .7rem; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar p-3" style="width: 260px; flex-shrink: 0;">
        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-mortarboard-fill fs-3"></i>
            <div>
                <div class="fw-bold">SPMB</div>
                <div class="small text-white-50">SMK Yadika Soreang</div>
            </div>
        </div>

        <a href="{{ route('spmb.admin.dashboard') }}" class="{{ request()->routeIs('spmb.admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="{{ route('spmb.admin.pendaftar.index') }}" class="{{ request()->routeIs('spmb.admin.pendaftar.index') ? 'active' : '' }}">
            <i class="bi bi-people-fill me-2"></i> Semua Pendaftar
        </a>

        <div class="section-label">Per Jurusan</div>
        @foreach($sidebarJurusan as $j)
            <a href="{{ route('spmb.admin.pendaftar.per-jurusan', $j) }}"
               class="d-flex justify-content-between align-items-center {{ request()->routeIs('spmb.admin.pendaftar.per-jurusan') && request()->route('jurusan')?->id === $j->id ? 'active' : '' }}">
                <span><i class="bi bi-diagram-3 me-2"></i>{{ $j->nama }}</span>
                <span class="badge bg-secondary badge-kuota">{{ $j->pendaftar_count }}</span>
            </a>
        @endforeach

        <div class="section-label">Export</div>
        <a href="{{ route('spmb.admin.export.excel') }}"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</a>
        <a href="{{ route('spmb.admin.export.pdf') }}"><i class="bi bi-file-earmark-pdf me-2"></i> Rekap PDF</a>

        <div class="section-label">Akun</div>
        <form action="{{ route('spmb.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light w-100 mt-1">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </nav>

    <main class="flex-grow-1 p-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
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
