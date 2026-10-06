<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring PKL Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        :root {
            --ocean-950: #0a192f;
            --ocean-900: #0c2340;
            --ocean-800: #10335e;
            --ocean-700: #174a85;
            --ocean-600: #1e62ad;
            --ocean-500: #257fdb;
            --sky-500: #38bdf8;
            --sky-400: #60a5fa;
            --sky-200: #bae6fd;
            --sky-100: #e0f2fe;
            --sky-50: #f0f9ff;
            --bg-page: #f4f8fc;
            --surface-card: #ffffff;
            --border-subtle: #e2eaf4;
            --text-main: #0f233a;
            --text-secondary: #4b647f;
            --text-muted: #829ab1;
            --shadow-sm: 0 2px 8px rgba(12, 35, 64, .04);
            --shadow-md: 0 8px 24px -4px rgba(12, 35, 64, .07), 0 2px 6px -1px rgba(12, 35, 64, .04);
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .app-layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 290px;
            flex-shrink: 0;
            background: linear-gradient(185deg, var(--ocean-950) 0%, var(--ocean-900) 60%, #0d2847 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 20;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--sky-500), var(--ocean-600));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(56, 189, 248, .35);
        }

        .brand-text h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
        }

        .brand-text p {
            font-size: .75rem;
            color: var(--sky-200);
            font-weight: 500;
            margin: 0;
        }

        .sidebar-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            gap: 24px;
        }

        .filter-section-title {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
            color: var(--sky-400);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: #dbeafe;
            margin-bottom: 6px;
        }

        .custom-select,
        .custom-input {
            width: 100%;
            padding: 10px 14px;
            background-color: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: var(--radius-sm);
            color: #fff;
            font-family: inherit;
            font-size: .88rem;
            outline: none;
            transition: all .2s ease;
        }

        .custom-select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23bae6fd'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .custom-input::placeholder {
            color: rgba(219, 234, 254, .55);
        }

        .custom-select:focus,
        .custom-input:focus {
            background-color: rgba(255, 255, 255, .12);
            border-color: var(--sky-400);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .2);
        }

        .custom-select option {
            background-color: var(--ocean-900);
            color: #fff;
        }

        .btn-filter {
            width: 100%;
            padding: 11px 16px;
            background: linear-gradient(135deg, var(--ocean-600), var(--ocean-500));
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 600;
            font-size: .88rem;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 127, 219, .3);
            transition: all .2s ease;
        }

        .btn-filter:hover {
            background: linear-gradient(135deg, var(--ocean-500), var(--sky-500));
            transform: translateY(-1px);
        }

        .btn-reset {
            display: block;
            text-align: center;
            margin-top: 10px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--sky-200);
            text-decoration: none;
        }

        .btn-reset:hover {
            color: #fff;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, .08);
        }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-action-apply,
        .btn-action-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 600;
            font-size: .88rem;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn-action-apply {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            border: 1px solid rgba(186, 230, 253, .25);
            box-shadow: 0 4px 14px rgba(2, 132, 199, .25);
        }

        .btn-action-apply:hover {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-action-login {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .15);
            color: #e0f2fe;
        }

        .btn-action-login:hover {
            background: rgba(255, 255, 255, .16);
            color: #fff;
        }

        /* MAIN */
        .main-content {
            flex-grow: 1;
            min-width: 0;
            padding: 32px 40px 60px;
            background: linear-gradient(180deg, #eaf2fb 0%, var(--bg-page) 260px);
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--ocean-950);
            letter-spacing: -.025em;
            margin-bottom: 6px;
        }

        .page-subtitle {
            font-size: .9rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        .search-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: var(--sky-50);
            border: 1px solid var(--sky-200);
            color: var(--ocean-700);
            padding: 10px 16px;
            border-radius: var(--radius-md);
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .search-banner a {
            color: var(--ocean-600);
            text-decoration: none;
            white-space: nowrap;
        }

        /* CARDS */
        .card-custom {
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .card-header-custom {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            background: linear-gradient(to right, #fff, #f7faff);
        }

        .header-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-badge-tag {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .header-title-wrap h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--ocean-950);
            margin: 0 0 2px;
        }

        .header-title-wrap p {
            font-size: .84rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .card-count-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
            border: 1px solid;
        }

        /* TABLE */
        table.modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        table.modern-table th {
            background: #f8fafc;
            color: var(--ocean-900);
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-subtle);
            white-space: nowrap;
        }

        table.modern-table td {
            padding: 16px 20px;
            font-size: .88rem;
            border-bottom: 1px solid var(--border-subtle);
            vertical-align: middle;
            background: #fff;
        }

        table.modern-table tbody tr:hover td {
            background: #f8fbff;
        }

        table.modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cell-student {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sky-200), var(--sky-400));
            color: var(--ocean-900);
            font-weight: 700;
            font-size: .82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .student-name {
            font-weight: 600;
            color: var(--ocean-950);
            display: block;
        }

        .student-nis {
            font-size: .75rem;
            color: var(--text-muted);
        }

        .badge-soft {
            display: inline-flex;
            padding: 5px 11px;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 600;
        }

        .badge-soft-jurusan {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .badge-soft-kelas {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .company-tag {
            font-weight: 600;
            color: var(--ocean-800);
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            min-width: 280px;
        }

        .approval-badge-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: .75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .status-approved {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .status-pending {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .approval-role {
            font-weight: 500;
        }

        .approval-state {
            font-weight: 700;
        }

        .status-pill-approved {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: .78rem;
            font-weight: 700;
        }

        .status-pill-approved .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }

        .table-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: #fafcff;
        }

        .table-footer-text {
            font-size: .82rem;
            color: var(--text-secondary);
        }

        .table-footer .pagination {
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px 16px !important;
            color: var(--text-muted);
        }

        @media (max-width: 991px) {
            .app-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }

            .main-content {
                padding: 24px 16px 40px;
            }

            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    @php
        $search = trim((string) request('search'));
        $adaFilter = $search !== '' || request('jurusan') || request('kelas');
    @endphp

    <div class="app-layout">

        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">🎓</div>
                <div class="brand-text">
                    <h2>Monitoring PKL</h2>
                    <p>SMK Yadika Soreang</p>
                </div>
            </div>

            <div class="sidebar-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="filter-section-title">Filter & Pencarian</div>

                    {{-- BARU: PENCARIAN NAMA --}}
                    <div class="form-group">
                        <label for="input-search">Cari Nama / NIS Siswa</label>
                        <input type="text" id="input-search" name="search" class="custom-input"
                            placeholder="Ketik nama siswa..." value="{{ request('search') }}" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="select-jurusan">Jurusan</label>
                        <select id="select-jurusan" name="jurusan" class="custom-select">
                            <option value="">Semua Jurusan</option>
                            @foreach ($jurusan as $j)
                                <option value="{{ $j->id }}" {{ request('jurusan') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="select-kelas">Kelas</label>
                        <select id="select-kelas" name="kelas" class="custom-select">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">🔍 Terapkan</button>

                    @if ($adaFilter)
                        <a href="{{ url()->current() }}" class="btn-reset">✕ Reset filter</a>
                    @endif
                </form>

                <div class="sidebar-divider"></div>

                <div class="quick-actions">
                    <span class="filter-section-title" style="margin-bottom:4px">Aksi Cepat</span>

                    <a href="{{ route('pkl.pengajuan.create') }}" class="btn-action-apply">
                        📄 Ajukan Tempat PKL
                    </a>

                    <a href="{{ route('pkl.login') }}" class="btn-action-login">
                        🔐 Login Sistem PKL
                    </a>
                </div>
            </div>
        </aside>

        {{-- CONTENT --}}
        <main class="main-content">

            <h1 class="page-title">Dasbor Monitoring PKL</h1>
            <p class="page-subtitle">Pantau alur persetujuan berkas magang & penempatan industri siswa.</p>

            @if ($search !== '')
                <div class="search-banner">
                    <span>Hasil pencarian untuk "<strong>{{ $search }}</strong>"</span>
                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null, 'page_disetujui' => null]) }}">Hapus pencarian</a>
                </div>
            @endif

            {{-- TABEL 1: BELUM DISETUJUI --}}
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="header-title-wrap">
                        <div class="header-badge-tag" style="background:#fffbeb;color:#d97706">⏳</div>
                        <div>
                            <h3>Status Pengajuan Belum Disetujui</h3>
                            <p>Siswa yang pengajuannya masih dalam proses approval, beserta tahap yang sedang ditunggu.</p>
                        </div>
                    </div>
                    <span class="card-count-badge"
                        style="background:#fffbeb;color:#b45309;border-color:#fde68a">
                        {{ $belumApproved->total() }} Menunggu
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Tempat PKL</th>
                                <th width="330">Menunggu Approval Dari</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Urutan tahap: Wali Kelas -> Guru BK -> Kesiswaan -> Kepala Jurusan
                                $tahapan = [
                                    'status_wali_kelas' => 'Wali Kelas',
                                    'status_guru_bk' => 'Guru BK',
                                    'status_kesiswaan' => 'Kesiswaan',
                                    'status_kepala_jurusan' => 'Kepala Jurusan',
                                ];
                            @endphp

                            @forelse ($belumApproved as $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">
                                        {{ $belumApproved->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <div class="cell-student">
                                            <div class="student-avatar">
                                                {{ strtoupper(mb_substr($item->siswa->nama ?? '?', 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="student-name">{{ $item->siswa->nama ?? '-' }}</span>
                                                @if ($item->siswa?->nis)
                                                    <span class="student-nis">NIS: {{ $item->siswa->nis }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-soft badge-soft-kelas">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                    <td><span class="badge-soft badge-soft-jurusan">{{ $item->siswa->kelas->jurusan->nama ?? '-' }}</span></td>
                                    <td><span class="company-tag">{{ $item->tempatPkl->nama_tempat ?? '-' }}</span></td>
                                    <td>
                                        <div class="approval-grid">
                                            @foreach ($tahapan as $kolom => $label)
                                                @php
                                                    $statusTahap = $item->{$kolom} ?? 'pending';
                                                    $kelasCss = match ($statusTahap) {
                                                        'approved' => 'status-approved',
                                                        'rejected' => 'status-rejected',
                                                        default => 'status-pending',
                                                    };
                                                    $keterangan = match ($statusTahap) {
                                                        'approved' => '✓ Disetujui',
                                                        'rejected' => '✕ Ditolak',
                                                        default => '⏳ Menunggu',
                                                    };
                                                @endphp
                                                <div class="approval-badge-item {{ $kelasCss }}">
                                                    <span class="approval-role">{{ $label }}</span>
                                                    <span class="approval-state">{{ $keterangan }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        {{ $search !== '' ? 'Tidak ada pengajuan yang cocok dengan pencarian.' : 'Tidak ada pengajuan yang sedang berjalan' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="table-footer-text">
                        Menampilkan <strong>{{ $belumApproved->firstItem() ?? 0 }} - {{ $belumApproved->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ $belumApproved->total() }}</strong> data
                    </div>
                    {{ $belumApproved->links() }}
                </div>
            </div>

            {{-- TABEL 2: DISETUJUI --}}
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="header-title-wrap">
                        <div class="header-badge-tag" style="background:#ecfdf5;color:#059669">✔</div>
                        <div>
                            <h3>Data Siswa PKL Disetujui</h3>
                            <p>Data siswa yang telah disetujui seluruh approval.</p>
                        </div>
                    </div>
                    <span class="card-count-badge"
                        style="background:#ecfdf5;color:#047857;border-color:#a7f3d0">
                        {{ $data->total() }} Siswa
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Guru Pembimbing</th>
                                <th>Tempat PKL</th>
                                <th width="150" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <div class="cell-student">
                                            <div class="student-avatar">
                                                {{ strtoupper(mb_substr($item->siswa->nama ?? '?', 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="student-name">{{ $item->siswa->nama ?? '-' }}</span>
                                                @if ($item->siswa?->nis)
                                                    <span class="student-nis">NIS: {{ $item->siswa->nis }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-soft badge-soft-kelas">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                    <td><span class="badge-soft badge-soft-jurusan">{{ $item->siswa->kelas->jurusan->nama ?? '-' }}</span></td>
                                    <td class="fw-semibold">{{ $item->guruPembimbing->nama ?? '-' }}</td>
                                    <td><span class="company-tag">{{ $item->tempatPkl->nama_tempat ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <span class="status-pill-approved"><span class="dot"></span>Approved</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        {{ $search !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Data kosong' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="table-footer-text">
                        Menampilkan <strong>{{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ $data->total() }}</strong> data
                    </div>
                    {{ $data->links() }}
                </div>
            </div>

        </main>
    </div>

</body>

</html>