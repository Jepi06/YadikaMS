<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Praktik - LMS SMK Yadika Soreang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link href="/css/nilaiGuruLms.css" rel="stylesheet">

    <style>
        .praktik-form { display: grid; grid-template-columns: 2fr 1fr 2fr auto; gap: 12px; align-items: end; }
        .praktik-form label { display: block; font-size: .78rem; font-weight: 600; color: var(--slate-700, #334155); margin-bottom: 4px; }
        .praktik-form input { width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 10px; font: inherit; font-size: .85rem; }
        @media (max-width: 800px) { .praktik-form { grid-template-columns: 1fr; } }
        .tabel-praktik { width: 100%; border-collapse: collapse; }
        .tabel-praktik th, .tabel-praktik td { box-sizing: border-box; vertical-align: middle; }
        .tabel-praktik th.col-siswa, .tabel-praktik td.col-siswa { min-width: 240px; text-align: left; }
        .tabel-praktik th.th-praktik, .tabel-praktik td.td-praktik { min-width: 190px; text-align: center; }
        .tabel-praktik th.col-rata, .tabel-praktik td.col-rata { min-width: 130px; text-align: center; }
        .th-praktik { vertical-align: top !important; padding: 10px; }
        .th-praktik input { display: block; width: 100%; min-width: 0; box-sizing: border-box; padding: 7px 8px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; font-size: .78rem; margin: 0 0 6px; background: #fff; color: #0f172a; text-align: center; }
        .th-praktik input[type="date"] { font-weight: 500; color: #475569; }
        .tabel-praktik td .table-input-number { width: 90px; margin: 0 auto; text-align: center; display: block; }
        .btn-hapus-praktik { display: inline-flex; align-items: center; gap: 4px; font-size: .72rem; font-weight: 600; color: #be123c; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 4px 8px; cursor: pointer; }
        .btn-hapus-praktik:hover { background: #be123c; color: #fff; }
        .rata-cell { text-align: center; font-weight: 700; color: var(--ocean-blue, #0d47a1); }
        .rata-cell small { display: block; font-weight: 500; color: var(--slate-400, #94a3b8); font-size: .7rem; }
        .btn-praktik-add { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border: 0; border-radius: 10px; font-weight: 700; font-size: .85rem; background: var(--ocean-blue, #0d47a1); color: #fff; cursor: pointer; }
        .hint-box { font-size: .78rem; color: var(--slate-500, #64748b); margin-top: 10px; }
    </style>
</head>
<body>

    <header class="site-header">
        <div class="container">
            <div class="navbar">
                <div class="brand-group">
                    <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <div class="brand-text">
                        <div class="name">LMS Yadika</div>
                        <div class="sub">Portal Guru & Nilai Akademik</div>
                    </div>
                </div>
                <div class="teacher-profile-chip">
                    <div class="teacher-avatar">G</div>
                    <div class="teacher-meta">
                        <span class="teacher-name">Guru Pengampu</span>
                        <span class="teacher-role">SMK Yadika Soreang</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="page-banner">
        <div class="container">
            <div class="banner-flex">
                <div class="mapel-info-group">
                    <div class="mapel-icon-large"><i class="bi bi-tools"></i></div>
                    <div>
                        <h1 class="banner-title">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h1>
                        <div class="banner-breadcrumbs">
                            <span class="class-pill">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }}</span>
                            <span>•</span>
                            <span>Nilai Praktik Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="banner-actions">
                    <a href="{{ route('lms.guru.nilai.index', $pengampuMapel) }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Rekap Nilai</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <main class="main-content">
        <div class="container">

            @if (session('status'))
                <div class="alert-status-success">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.15rem;"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-status-error">
                    <i class="bi bi-exclamation-circle-fill" style="font-size: 1.15rem;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- CARD 1: TAMBAH PRAKTIK -->
            <div class="card-bobot">
                <div class="card-header-styled">
                    <div class="header-title-flex">
                        <div class="header-icon-pill"><i class="bi bi-plus-circle"></i></div>
                        <span>Tambah Kegiatan Praktik</span>
                    </div>
                    <span class="header-hint">Bisa ditambah berkali-kali (Praktik 1, Praktik 2, dst.)</span>
                </div>
                <div class="card-body-styled">
                    <form method="POST" action="{{ route('lms.guru.nilai.praktik.store', $pengampuMapel) }}" class="praktik-form">
                        @csrf
                        <div>
                            <label for="judul">Judul Praktik</label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Praktik 1 - CRUD Laravel" required maxlength="255">
                        </div>
                        <div>
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}">
                        </div>
                        <div>
                            <label for="deskripsi">Keterangan (opsional)</label>
                            <input type="text" id="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}" maxlength="1000">
                        </div>
                        <div>
                            <button type="submit" class="btn-praktik-add">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CARD 2: MATRIKS NILAI PRAKTIK -->
            <form method="POST" action="{{ route('lms.guru.nilai.praktik.simpan', $pengampuMapel) }}">
                @csrf
                <div class="card-table-wrap">
                    <div class="card-header-styled">
                        <div class="header-title-flex">
                            <div class="header-icon-pill"><i class="bi bi-table"></i></div>
                            <span>Input Nilai Praktik</span>
                        </div>
                        <span class="header-hint">
                            Rata-rata = jumlah nilai ÷ jumlah praktik yang terisi. Kosongkan sel jika siswa belum/tidak dinilai.
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table tabel-praktik" style="min-width: {{ 240 + 190 * $daftarPraktik->count() + 130 }}px;">
                            <thead>
                                <tr>
                                    <th class="col-siswa">Siswa</th>
                                    @foreach ($daftarPraktik as $p)
                                        <th class="th-praktik">
                                            <input type="text" name="praktik[{{ $p->id }}][judul]" value="{{ $p->judul }}" required maxlength="255" title="Judul praktik">
                                            <input type="date" name="praktik[{{ $p->id }}][tanggal]" value="{{ optional($p->tanggal)->format('Y-m-d') }}" title="Tanggal praktik">
                                            <button type="submit"
                                                    form="hapus-praktik-{{ $p->id }}"
                                                    class="btn-hapus-praktik"
                                                    onclick="return confirm('Hapus praktik &quot;{{ addslashes($p->judul) }}&quot; beserta semua nilainya?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </th>
                                    @endforeach
                                    <th class="col-rata">Rata-rata Praktik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswaList as $siswa)
                                    @php $nilaiSiswa = $nilai->get($siswa->id, collect()); @endphp
                                    <tr class="js-row">
                                        <td class="col-siswa">
                                            <div class="student-cell">
                                                <div class="student-avatar">{{ strtoupper(substr($siswa->nama ?? 'S', 0, 1)) }}</div>
                                                <div><span class="student-name-text">{{ $siswa->nama }}</span></div>
                                            </div>
                                        </td>
                                        @foreach ($daftarPraktik as $p)
                                            <td class="td-praktik">
                                                <input type="number"
                                                       name="nilai[{{ $siswa->id }}][{{ $p->id }}]"
                                                       class="table-input-number js-nilai"
                                                       value="{{ old("nilai.{$siswa->id}.{$p->id}", $nilaiSiswa->get($p->id) !== null ? (float) $nilaiSiswa->get($p->id) : '') }}"
                                                       min="0" max="100" step="0.1" placeholder="-">
                                            </td>
                                        @endforeach
                                        <td class="rata-cell col-rata">
                                            <span class="js-rata">-</span>
                                            <small class="js-jumlah"></small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $daftarPraktik->count() + 2 }}" class="empty-state-row">
                                            <i class="bi bi-people" style="font-size: 2rem; display: block; margin-bottom: 8px; color: var(--slate-300);"></i>
                                            Belum ada data siswa di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($daftarPraktik->isEmpty())
                        <div class="empty-state-row" style="padding: 18px;">
                            Belum ada kegiatan praktik. Tambahkan praktik pertama lewat form di atas.
                        </div>
                    @elseif ($siswaList->count())
                        <div class="table-action-footer">
                            <div class="footer-left-action">
                                <button type="submit" class="btn-submit-primary">
                                    <i class="bi bi-save2-fill"></i>
                                    <span>Simpan Nilai Praktik</span>
                                </button>
                                <div class="footer-update-note">
                                    <i class="bi bi-shield-check" style="color: var(--emerald-text);"></i>
                                    <span>Judul &amp; tanggal praktik juga ikut tersimpan.</span>
                                </div>
                            </div>
                            <div style="font-size: 0.775rem; color: var(--slate-400);">
                                {{ $daftarPraktik->count() }} praktik • {{ $siswaList->count() }} siswa
                            </div>
                        </div>
                    @endif
                </div>
            </form>

            {{-- Form hapus ditaruh di luar form utama (HTML tidak boleh form bersarang) --}}
            @foreach ($daftarPraktik as $p)
                <form id="hapus-praktik-{{ $p->id }}" method="POST" action="{{ route('lms.guru.nilai.praktik.destroy', [$pengampuMapel, $p]) }}" hidden>
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach

        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-flex">
                <div class="footer-brand">
                    <div class="footer-badge-icon">Y</div>
                    <strong style="color: var(--slate-900);">SMK Yadika Soreang</strong>
                    <span>|</span>
                    <span>Learning Management System — Modul Guru Pengampu</span>
                </div>
                <div>&copy; 2025 SMK Yadika Soreang. Hak Cipta Dilindungi.</div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            // Hitung rata-rata per siswa secara langsung saat mengetik
            function hitungBaris(row) {
                let total = 0, jumlah = 0;
                row.querySelectorAll('.js-nilai').forEach(function (el) {
                    if (el.value !== '' && !isNaN(parseFloat(el.value))) {
                        total += parseFloat(el.value);
                        jumlah++;
                    }
                });
                row.querySelector('.js-rata').textContent = jumlah ? (total / jumlah).toFixed(1) : '-';
                row.querySelector('.js-jumlah').textContent = jumlah ? (jumlah + ' praktik') : '';
            }

            document.querySelectorAll('.js-row').forEach(function (row) {
                hitungBaris(row);
                row.querySelectorAll('.js-nilai').forEach(function (el) {
                    el.addEventListener('input', function () { hitungBaris(row); });
                });
            });
        })();
    </script>
</body>
</html>