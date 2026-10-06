{{-- resources/views/Mapping/public/mou.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitra Industri & MOU — SMK Yadika Soreang</title>
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
            --sky-50: #f0f9ff;
            --bg-page: #f4f8fc;
            --border-subtle: #e2eaf4;
            --text-secondary: #4b647f;
            --text-muted: #829ab1;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-page);
            color: #0f233a;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .hero {
            background: linear-gradient(185deg, var(--ocean-950) 0%, var(--ocean-900) 60%, #0d2847 100%);
            color: #fff;
            padding: 28px 0 90px;
        }

        .hero-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 36px;
        }

        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #fff; }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--sky-500), var(--ocean-600));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(56, 189, 248, .35);
        }

        .brand-text strong { display: block; font-size: 1.05rem; line-height: 1.1; }
        .brand-text small { color: var(--sky-200); font-size: .75rem; }

        .hero-links { display: flex; gap: 8px; flex-wrap: wrap; }

        .hero-link {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .08);
            color: #e0f2fe;
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
        }

        .hero-link:hover { background: rgba(255, 255, 255, .16); color: #fff; }

        .hero h1 { font-size: 1.9rem; font-weight: 800; letter-spacing: -.025em; margin-bottom: 8px; }
        .hero p { color: var(--sky-200); max-width: 620px; margin: 0; }

        .stats {
            margin-top: -56px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .stat {
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 8px 24px -4px rgba(12, 35, 64, .08);
        }

        .stat .num { font-size: 1.7rem; font-weight: 800; color: var(--ocean-950); line-height: 1.1; }
        .stat .lbl { font-size: .8rem; color: var(--text-secondary); font-weight: 600; }

        .filter-bar {
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 14px 16px;
            margin: 24px 0 20px;
        }

        .mitra-card {
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(12, 35, 64, .04);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .mitra-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-subtle);
            background: linear-gradient(to right, #fff, #f7faff);
        }

        .mitra-head h3 { font-size: 1.05rem; font-weight: 700; margin: 0 0 2px; color: var(--ocean-950); }
        .mitra-head .meta { font-size: .8rem; color: var(--text-secondary); }

        .bidang-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--sky-50);
            border: 1px solid var(--sky-200);
            color: var(--ocean-700);
            font-size: .72rem;
            font-weight: 700;
            margin-left: 8px;
            vertical-align: middle;
        }

        .mou-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            padding: 14px 22px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .mou-row:last-child { border-bottom: none; }

        .mou-title { font-weight: 600; font-size: .9rem; }
        .mou-sub { font-size: .78rem; color: var(--text-muted); }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            border: 1px solid;
            margin-right: 6px;
        }

        .pill-aktif { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
        .pill-berakhir { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
        .pill-tanpa { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

        .btn-berkas {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--ocean-600), var(--ocean-500));
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 127, 219, .25);
        }

        .btn-berkas:hover { color: #fff; transform: translateY(-1px); }

        .berkas-private {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: var(--text-muted);
            font-size: .78rem;
            font-weight: 600;
        }

        .empty {
            text-align: center;
            padding: 56px 16px;
            color: var(--text-muted);
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
        }

        @media (max-width: 767px) {
            .stats { grid-template-columns: 1fr; margin-top: -48px; }
            .hero h1 { font-size: 1.5rem; }
        }
    </style>
</head>

<body>
    <header class="hero">
        <div class="container" style="max-width: 1000px;">
            <div class="hero-nav">
                <a href="{{ route('pkl') }}" class="brand">
                    <div class="brand-icon">🎓</div>
                    <div class="brand-text">
                        <strong>Monitoring PKL</strong>
                        <small>SMK Yadika Soreang</small>
                    </div>
                </a>
                <div class="hero-links">
                    <a href="{{ route('pkl') }}" class="hero-link">📊 Dashboard PKL</a>
                    <a href="{{ route('pkl.pengajuan.create') }}" class="hero-link">📄 Ajukan Tempat PKL</a>
                    <a href="{{ route('pkl.login') }}" class="hero-link">🔐 Login</a>
                </div>
            </div>

            <h1>Mitra Industri & MOU</h1>
            <p>Daftar dunia usaha dan dunia industri yang sudah menjalin kerja sama (MOU) dengan sekolah kami.</p>
        </div>
    </header>

    <main class="container pb-5" style="max-width: 1000px;">

        <div class="stats">
            <div class="stat">
                <div class="num">{{ $totalMitra }}</div>
                <div class="lbl">Mitra Industri</div>
            </div>
            <div class="stat">
                <div class="num">{{ $totalMou }}</div>
                <div class="lbl">Total MOU</div>
            </div>
            <div class="stat">
                <div class="num">{{ $totalPublik }}</div>
                <div class="lbl">Berkas Dipublikasikan</div>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('pkl.mou.public') }}" class="filter-bar">
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama perusahaan / bidang usaha..."
                        value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua status MOU</option>
                        <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>MOU aktif</option>
                        <option value="berakhir" {{ $status === 'berakhir' ? 'selected' : '' }}>MOU berakhir</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                    @if ($search !== '' || $status)
                        <a href="{{ route('pkl.mou.public') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Daftar mitra --}}
        @forelse ($tempat as $t)
            <article class="mitra-card">
                <div class="mitra-head">
                    <h3>
                        {{ $t->nama_tempat }}
                        @if ($t->bidang_usaha)
                            <span class="bidang-pill">{{ $t->bidang_usaha }}</span>
                        @endif
                    </h3>
                    <div class="meta">📍 {{ $t->alamat }}</div>
                </div>

                @foreach ($t->mou as $m)
                    @php
                        [$kelasPill, $labelPill] = match ($m->status) {
                            'aktif' => ['pill-aktif', 'MOU Aktif'],
                            'berakhir' => ['pill-berakhir', 'Berakhir'],
                            default => ['pill-tanpa', 'Tanpa batas waktu'],
                        };
                    @endphp
                    <div class="mou-row">
                        <div>
                            <div class="mou-title">{{ $m->judul ?: 'Memorandum of Understanding' }}</div>
                            <div class="mou-sub">
                                @if ($m->nomor_mou)
                                    No. {{ $m->nomor_mou }} ·
                                @endif
                                @if ($m->tanggal_mulai || $m->tanggal_berakhir)
                                    {{ $m->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                    s/d
                                    {{ $m->tanggal_berakhir?->format('d/m/Y') ?? 'seterusnya' }}
                                @else
                                    Periode belum dicantumkan
                                @endif
                            </div>
                            <div class="mt-2">
                                <span class="pill {{ $kelasPill }}">{{ $labelPill }}</span>
                            </div>
                        </div>

                        <div>
                            @if ($m->is_public)
                                <a href="{{ route('pkl.mou.public.berkas', $m) }}" target="_blank" class="btn-berkas">
                                    📄 Lihat Berkas MOU
                                </a>
                            @else
                                <span class="berkas-private">🔒 Berkas tidak dipublikasikan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </article>
        @empty
            <div class="empty">
                @if ($search !== '' || $status)
                    Tidak ada mitra yang cocok dengan pencarian.
                @else
                    Belum ada data MOU yang dicatat.
                @endif
            </div>
        @endforelse

        <div class="mt-3">{{ $tempat->links() }}</div>
    </main>
</body>

</html>