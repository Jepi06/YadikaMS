<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Super Admin') - SMK Yadika Soreang</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tailwind CDN for preview compatibility -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        ocean: {
                            800: '#0f2f5a',
                            900: '#0a1d37',
                            950: '#061325',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif']
                    },
                },
            },
        }
    </script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }

        /* Pertahankan font icon Bootstrap Icons (selector * di atas menimpanya) */
        .bi,
        [class^="bi-"],
        [class*=" bi-"] {
            font-family: 'bootstrap-icons' !important;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            background: linear-gradient(180deg, #0b1528 0%, #0f1d36 60%, #070e1c 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: block;
            text-decoration: none;
        }

        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem 0.65rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: #94a3b8;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 0.65rem 0.85rem;
            border-radius: 0.65rem;
            margin-bottom: 0.2rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            font-size: 1.15rem;
            margin-right: 0.75rem;
            transition: transform 0.2s ease, color 0.2s ease;
            opacity: 0.85;
        }

        .sidebar .nav-link:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.07);
            transform: translateX(2px);
        }

        .sidebar .nav-link:hover i {
            color: #38bdf8;
            opacity: 1;
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            font-weight: 600;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.85) 0%, rgba(29, 78, 216, 0.95) 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .sidebar .nav-link.active i {
            color: #ffffff;
            opacity: 1;
        }

        .nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            padding: 0.75rem 0.85rem 0.35rem;
        }

        /* Topbar Styling */
        .topbar {
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 1020;
            border-bottom: 1px solid #e2e8f0;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }

        .main-content {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(3px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Responsive behavior */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            /* Mobile: tombol logout tampil di atas "Menu Utama" (di bawah brand) */
            .sidebar .sidebar-brand {
                order: 1 !important;
            }

            .sidebar .sidebar-logout {
                order: 2 !important;
                border-top-width: 0 !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            }

            .sidebar .sidebar-nav-container {
                order: 3 !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php $superAdmin = request()->attributes->get('superAdminUser'); @endphp

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <!-- Brand Header -->
        <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
            <div class="d-flex align-items-center gap-2">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-sky-400 d-flex align-items-center justify-content-center text-white shadow-md shadow-blue-600/30">
                    <i class="bi bi-shield-lock-fill text-lg"></i>
                </div>
                <div>
                    <span class="text-white font-bold tracking-tight block text-base leading-tight">SMK Yadika</span>
                    <span class="text-xs font-semibold text-sky-400 tracking-wide uppercase">Super Admin</span>
                </div>
            </div>
            <div
                class="mt-2.5 px-2.5 py-1 rounded bg-slate-900/60 border border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Lintas Modul</span>
                <span class="text-sky-300 font-medium font-mono">PKL · SPMB · LMS</span>
            </div>
        </a>

        <!-- Navigation Links -->
        <div class="sidebar-nav-container custom-scrollbar">
            <div class="nav-section-title">Menu Utama</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-title mt-2">Manajemen Akademik</div>
            <a class="nav-link {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}"
                href="{{ route('admin.pengguna.index') }}">
                <i class="bi bi-people-fill"></i>
                <span>Kelola Pengguna</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"
                href="{{ route('admin.siswa.index') }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Kelola Siswa</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.kenaikan-kelas.*') ? 'active' : '' }}"
                href="{{ route('admin.kenaikan-kelas.index') }}">
                <i class="bi bi-arrow-up-circle"></i>
                <span>Kenaikan Kelas</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}"
                href="{{ route('admin.kelas.index') }}">
                <i class="bi bi-door-open-fill"></i>
                <span>Kelola Kelas</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"
                href="{{ route('admin.guru.index') }}">
                <i class="bi bi-person-video3"></i>
                <span>Kelola Guru</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.mata-pelajaran.*') ? 'active' : '' }}"
                href="{{ route('admin.mata-pelajaran.index') }}">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>Mata Pelajaran</span>
            </a>

            <div class="nav-section-title mt-2">Aktivitas &amp; Kurikulum</div>
            <a class="nav-link {{ request()->routeIs('admin.modul-ajar.*') ? 'active' : '' }}"
                href="{{ route('admin.modul-ajar.index') }}">
                <i class="bi bi-archive-fill"></i>
                <span>Arsip Modul Ajar</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.monitoring-presensi.*') ? 'active' : '' }}"
                href="{{ route('admin.monitoring-presensi.index') }}">
                <i class="bi bi-broadcast"></i>
                <span>Monitoring Presensi</span>
                <span
                    class="ms-auto badge bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded-full border border-emerald-400/30">Live</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}"
                href="{{ route('admin.jadwal.index') }}">
                <i class="bi bi-calendar-week"></i>
                <span>Jadwal Guru</span>
            </a>
            {{-- <a class="nav-link {{ request()->routeIs('admin.rekap-absensi.*') ? 'active' : '' }}" href="{{ route('admin.rekap-absensi.index') }}">
                <i class="bi bi-calendar-check"></i>
                <span>Rekap Absensi</span>
            </a> --}}

            <div class="nav-section-title mt-2">Akun</div>
            <a class="nav-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}"
                href="{{ route('admin.profil.edit') }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
        </div>

        <!-- Logout Bottom Section -->
        <div class="sidebar-logout p-3 border-t border-slate-800/80 bg-slate-950/40">
            <form action="{{ route('admin.logout') }}" class="m-0" method="POST">
                @csrf
                <button
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-lg border-0 bg-transparent text-sm font-medium transition duration-150"
                    type="submit">
                    <i class="bi bi-box-arrow-left text-base"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="main-content">

        <!-- TOPBAR -->
        <header class="topbar px-3.5 sm:px-6 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <!-- Mobile Sidebar Toggle -->
                <button aria-label="Buka Navigasi"
                    class="btn btn-sm btn-light border d-lg-none d-flex align-items-center justify-content-center text-slate-700 shadow-sm"
                    id="sidebarToggle" type="button">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <span
                        class="badge bg-blue-50 text-blue-700 border border-blue-200/70 d-none d-sm-inline-flex px-2 py-1 text-xs font-semibold">
                        SMK Yadika Soreang
                    </span>
                    <h1 class="text-sm sm:text-base font-semibold text-slate-800 m-0 leading-none">
                        Panel Super Admin
                    </h1>
                </div>
            </div>

            <!-- Topbar Kanan: Profile & Badge -->
            <div class="d-flex align-items-center gap-2 sm:gap-3">
                <!-- Quick indicator -->
                <div
                    class="d-none d-sm-flex align-items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tahun Ajaran 2024/2025</span>
                </div>

                <a class="d-flex align-items-center gap-2 text-decoration-none p-1 sm:p-1.5 rounded-lg hover:bg-slate-100 transition"
                    href="{{ route('admin.profil.edit') }}">
                    @if ($superAdmin?->avatar)
                        <img alt="Avatar" class="rounded-full ring-2 ring-blue-100 object-cover"
                            src="{{ asset('storage/' . $superAdmin->avatar) }}" style="width:34px;height:34px">
                    @else
                        <div class="rounded-full bg-gradient-to-tr from-slate-900 to-slate-700 text-white d-inline-flex align-items-center justify-content-center fw-bold text-xs ring-2 ring-slate-200"
                            style="width:34px;height:34px">
                            {{ strtoupper(substr($superAdmin?->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div class="d-none d-md-block text-start leading-tight">
                        <span class="block text-xs font-bold text-slate-800 truncate max-w-[140px]">
                            {{ $superAdmin?->name ?? 'Super Administrator' }}
                        </span>
                        <span
                            class="text-[11px] text-slate-500 font-medium">{{ $superAdmin?->email ?? 'superadmin@yadika.sch.id' }}</span>
                    </div>
                </a>

                <span class="badge bg-slate-900 text-slate-100 font-semibold px-2.5 py-1 text-xs shadow-sm">
                    Super Admin
                </span>
            </div>
        </header>

        <!-- BACKDROP FOR MOBILE -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN CONTENT AREA -->
        <main class="p-3.5 sm:p-6 lg:p-8 flex-grow">

            <!-- Flash Message / Status Alert -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-xl d-flex align-items-center gap-2 mb-4 bg-emerald-50 text-emerald-800 border-l-4 border-l-emerald-500"
                    role="alert">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                    <div class="text-sm font-medium">{{ session('status') }}</div>
                    <button aria-label="Close" class="btn-close ms-auto" data-bs-dismiss="alert"
                        type="button"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-xl d-flex align-items-center gap-2 mb-4 bg-emerald-50 text-emerald-800 border-l-4 border-l-emerald-500"
                    role="alert">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                    <button aria-label="Close" class="btn-close ms-auto" data-bs-dismiss="alert"
                        type="button"></button>
                </div>
            @endif

            {{-- Konten halaman; jika kosong, tampilkan preview dashboard default --}}
            @hasSection('content')
                @yield('content')
            @else
                <div class="space-y-6">
                    <!-- Welcome Banner -->
                    <div
                        class="rounded-2xl bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 text-white p-5 sm:p-6 shadow-lg shadow-blue-500/20 relative overflow-hidden">
                        <div class="relative z-10 max-w-2xl">
                            <span
                                class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-semibold backdrop-blur-sm inline-block mb-2">
                                SMK Yadika Soreang Cloud Management
                            </span>
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang di Panel Super
                                Admin</h2>
                            <p class="text-blue-100 text-xs sm:text-sm mt-1 leading-relaxed">
                                Pusat kendali operasional integrasi multi-modul: Praktik Kerja Lapangan (PKL), Sistem
                                Penerimaan Murid Baru (SPMB), dan Learning Management System (LMS).
                            </p>
                        </div>
                        <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none">
                            <i class="bi bi-shield-lock-fill text-9xl"></i>
                        </div>
                    </div>

                    <!-- Metric Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <div
                            class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-slate-500">Total Siswa Aktif</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="bi bi-people text-sm"></i>
                                </div>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-slate-800">1,248</div>
                            <div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                                <i class="bi bi-arrow-up-short text-base"></i> +32 siswa baru
                            </div>
                        </div>

                        <div
                            class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-slate-500">Guru &amp; Tenaga Didik</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <i class="bi bi-person-video3 text-sm"></i>
                                </div>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-slate-800">86</div>
                            <div class="text-[11px] text-slate-500 font-medium mt-1">12 Jurusan Terdaftar</div>
                        </div>

                        <div
                            class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-slate-500">Presensi Hari Ini</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="bi bi-broadcast text-sm"></i>
                                </div>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-slate-800">97.4%</div>
                            <div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Presensi berjalan tertib
                            </div>
                        </div>

                        <div
                            class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-slate-500">Modul Terarsip</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <i class="bi bi-archive text-sm"></i>
                                </div>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-slate-800">342</div>
                            <div class="text-[11px] text-amber-600 font-medium mt-1">18 revisi kurikulum</div>
                        </div>
                    </div>

                    <!-- Quick Action Grid -->
                    <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                            <i class="bi bi-lightning-charge-fill text-amber-500"></i>
                            Akses Cepat Pengelolaan
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <a class="p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-blue-50/60 hover:border-blue-200 transition text-decoration-none group"
                                href="{{ route('admin.siswa.index') }}">
                                <i
                                    class="bi bi-person-lines-fill text-slate-600 group-hover:text-blue-600 text-lg"></i>
                                <div class="text-xs font-semibold text-slate-800 mt-1.5">Input Siswa Baru</div>
                                <div class="text-[10px] text-slate-500">Registrasi &amp; biodata</div>
                            </a>
                            <a class="p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-blue-50/60 hover:border-blue-200 transition text-decoration-none group"
                                href="{{ route('admin.jadwal.index') }}">
                                <i class="bi bi-calendar-week text-slate-600 group-hover:text-blue-600 text-lg"></i>
                                <div class="text-xs font-semibold text-slate-800 mt-1.5">Plot Jadwal Guru</div>
                                <div class="text-[10px] text-slate-500">Kalender semester</div>
                            </a>
                            <a class="p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-blue-50/60 hover:border-blue-200 transition text-decoration-none group"
                                href="{{ route('admin.monitoring-presensi.index') }}">
                                <i class="bi bi-broadcast text-slate-600 group-hover:text-blue-600 text-lg"></i>
                                <div class="text-xs font-semibold text-slate-800 mt-1.5">Live Monitoring</div>
                                <div class="text-[10px] text-slate-500">Kehadiran kelas &amp; PKL</div>
                            </a>
                            <a class="p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:bg-blue-50/60 hover:border-blue-200 transition text-decoration-none group"
                                href="{{ route('admin.kenaikan-kelas.index') }}">
                                <i class="bi bi-arrow-up-circle text-slate-600 group-hover:text-blue-600 text-lg"></i>
                                <div class="text-xs font-semibold text-slate-800 mt-1.5">Kenaikan Kelas</div>
                                <div class="text-[10px] text-slate-500">Proses evaluasi akhir</div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Scripts -->
    <script>
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        backdrop?.addEventListener('click', closeSidebar);

        // Close on window resize if larger than mobile breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Popup wajib ganti password --}}
    @include('partials.wajib-ganti-password', [
        'user' => $superAdmin,
        'profilUrl' => route('admin.profil.edit'),
        'sedangDiProfil' => request()->routeIs('admin.profil.*'),
    ])

    @stack('scripts')
</body>

</html>