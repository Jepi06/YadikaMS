@extends('lms.layouts.app')

@section('title', 'Rekap Absensi Wali Kelas')

@section('content')
    <div class="page-header d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar-check me-1 text-primary"></i> Rekap Absensi — Wali Kelas</h4>
            <p class="text-muted mb-0">{{ $kelas->nama_kelas }}</p>
        </div>
        <a href="{{ route('lms.guru.wali-kelas.index', ['kelas_id' => $kelas->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester]) }}"
            class="btn btn-sm btn-outline-primary">
            <i class="bi bi-clipboard-data"></i> Lihat Rekap Nilai
        </a>
    </div>

    <form method="GET" class="card mb-4 mt-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                @if ($kelasDiwalikan->count() > 1)
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Kelas</label>
                        <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach ($kelasDiwalikan as $k)
                                <option value="{{ $k->id }}" @selected($k->id === $kelas->id)>{{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                @endif

                <div class="col-md-3">
                    <label class="form-label small mb-1">Tahun Ajaran</label>
                    @php
                        $opsiTahun = $periodeList->pluck('tahun_ajaran')->unique();
                        if ($opsiTahun->isEmpty()) {
                            $opsiTahun = collect([$tahunAjaran]);
                        }
                    @endphp
                    <select name="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($opsiTahun as $ta)
                            <option value="{{ $ta }}" @selected($ta === $tahunAjaran)>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach (['Ganjil', 'Genap'] as $s)
                            <option value="{{ $s }}" @selected($s === $semester)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Bulan (Detail Harian)</label>
                    <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($hariMasukPerBulan as $bulan => $hm)
                            <option value="{{ $bulan }}" @selected($bulan === $bulanDipilih)>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    @if ($hariMasukPerBulan->isEmpty())
        <div class="card">
            <div class="card-body empty-state">
                <i class="bi bi-inbox"></i>
                Belum ada data presensi untuk periode {{ $tahunAjaran }} {{ $semester }}.
            </div>
        </div>
    @else
        {{-- DETAIL HARIAN --}}
        @php
            $badgeStatus = [
                'Hadir' => ['kode' => 'H', 'class' => 'bg-success'],
                'Izin' => ['kode' => 'I', 'class' => 'bg-info text-dark'],
                'Sakit' => ['kode' => 'S', 'class' => 'bg-warning text-dark'],
                'Alpa' => ['kode' => 'A', 'class' => 'bg-danger'],
            ];
        @endphp

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 mt-3">
            <h5 class="fw-bold mb-0">
                Detail Harian —
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanDipilih)->translatedFormat('F Y') }}
            </h5>
            <div class="small d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-success">H</span> Hadir
                <span class="badge bg-info text-dark">I</span> Izin
                <span class="badge bg-warning text-dark">S</span> Sakit
                <span class="badge bg-danger">A</span> Alpa
                <span class="text-muted">· "–" = belum ada presensi</span>
            </div>
        </div>

        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0 text-center" style="font-size:.82rem">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-start"
                                style="position:sticky;left:0;background:#f8f9fa;min-width:180px;z-index:2">Siswa</th>
                            @foreach ($tanggalList as $tgl)
                                @php $c = \Carbon\Carbon::parse($tgl); @endphp
                                <th style="min-width:38px">{{ $c->format('d') }}</th>
                            @endforeach
                            <th class="bg-success-subtle">H</th>
                            <th class="bg-info-subtle">I</th>
                            <th class="bg-warning-subtle">S</th>
                            <th class="bg-danger-subtle">A</th>
                        </tr>
                        <tr>
                            @foreach ($tanggalList as $tgl)
                                <th class="fw-normal text-muted" style="font-size:.7rem">
                                    {{ \Carbon\Carbon::parse($tgl)->translatedFormat('D') }}
                                </th>
                            @endforeach
                            <th colspan="4" class="fw-normal text-muted" style="font-size:.7rem">Total hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $r)
                            @php
                                $dataSiswa = $detailHarian[$r->siswa->id] ?? [];
                                $hitung = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
                                foreach ($dataSiswa as $d) {
                                    if (isset($hitung[$d['status']])) {
                                        $hitung[$d['status']]++;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-start fw-semibold" style="position:sticky;left:0;background:#fff;z-index:1">
                                    {{ $r->siswa->nama }}
                                </td>
                                @foreach ($tanggalList as $tgl)
                                    @php $cell = $dataSiswa[$tgl] ?? null; @endphp
                                    <td class="p-1">
                                        @if ($cell && isset($badgeStatus[$cell['status']]))
                                            <span class="badge {{ $badgeStatus[$cell['status']]['class'] }}"
                                                title="{{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}: {{ $cell['rincian'] }}">
                                                {{ $badgeStatus[$cell['status']]['kode'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">–</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="fw-semibold bg-success-subtle">{{ $hitung['Hadir'] }}</td>
                                <td class="fw-semibold bg-info-subtle">{{ $hitung['Izin'] }}</td>
                                <td class="fw-semibold bg-warning-subtle">{{ $hitung['Sakit'] }}</td>
                                <td class="fw-semibold bg-danger-subtle">{{ $hitung['Alpa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-secondary small mb-4 d-flex align-items-start gap-2">
            <i class="bi bi-info-circle mt-1"></i>
            <div>Jika dalam satu hari siswa punya status berbeda di beberapa mapel, yang ditampilkan adalah status terberat (Alpa &gt; Sakit &gt; Izin &gt; Hadir). Arahkan kursor ke kotak untuk melihat rinciannya.</div>
        </div>

        {{-- REKAP PER BULAN --}}
        <h5 class="fw-bold mb-3"><i class="bi bi-chart-bar me-1 text-primary"></i> Rekap Persentase Per Siswa</h5>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="position:sticky;left:0;background:#f8f9fa;">Siswa</th>
                            @foreach ($hariMasukPerBulan as $bulan => $hariMasuk)
                                <th class="text-center">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y') }}
                                    <div class="small fw-normal text-muted">{{ $hariMasuk }} hari masuk</div>
                                </th>
                            @endforeach
                            <th class="text-center bg-primary-subtle">
                                Keseluruhan
                                <div class="small fw-normal text-muted">{{ $totalHariMasuk }} hari masuk</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $r)
                            <tr>
                                <td style="position:sticky;left:0;background:#fff;" class="fw-semibold">{{ $r->siswa->nama }}</td>
                                @foreach ($r->per_bulan as $data)
                                    @php
                                        $warna =
                                            $data['persen'] >= 90
                                                ? 'success'
                                                : ($data['persen'] >= 75
                                                    ? 'warning'
                                                    : 'danger');
                                    @endphp
                                    <td class="text-center">
                                        <span class="badge bg-{{ $warna }}-subtle text-{{ $warna }}-emphasis">
                                            {{ $data['persen'] }}%
                                        </span>
                                        <div class="small text-muted">{{ $data['hadir'] }}/{{ $data['hari_masuk'] }}</div>
                                    </td>
                                @endforeach
                                @php
                                    $warnaTotal =
                                        $r->total_persen >= 90
                                            ? 'success'
                                            : ($r->total_persen >= 75
                                                ? 'warning'
                                                : 'danger');
                                @endphp
                                <td class="text-center bg-primary-subtle">
                                    <span class="badge bg-{{ $warnaTotal }} fs-6">{{ $r->total_persen }}%</span>
                                    <div class="small text-muted">{{ $r->total_hadir }}/{{ $r->total_hari_masuk }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REKAP KELAS --}}
        <h5 class="fw-bold mt-5 mb-3"><i class="bi bi-building me-1 text-primary"></i> Rekap Keseluruhan Kelas</h5>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapKelasPerBulan as $bulan => $d)
                            <tr>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y') }}
                                    <div class="small fw-normal text-muted">{{ $d['hari_aktif'] }} hari aktif ×
                                        {{ $kelas->siswa->count() }} siswa = {{ $d['slot'] }} slot</div>
                                </td>
                                <td class="text-center"><span class="fw-semibold">{{ $d['hadir'] }}</span> <span
                                        class="text-muted small">({{ $d['persen_hadir'] }}%)</span></td>
                                <td class="text-center"><span class="fw-semibold">{{ $d['izin'] }}</span> <span
                                        class="text-muted small">({{ $d['persen_izin'] }}%)</span></td>
                                <td class="text-center"><span class="fw-semibold">{{ $d['sakit'] }}</span> <span
                                        class="text-muted small">({{ $d['persen_sakit'] }}%)</span></td>
                                <td class="text-center"><span class="fw-semibold {{ $d['alpa'] > 0 ? 'text-danger' : '' }}">{{ $d['alpa'] }}</span> <span
                                        class="text-muted small">({{ $d['persen_alpa'] }}%)</span></td>
                            </tr>
                        @endforeach
                        <tr class="table-primary fw-bold">
                            <td>
                                Keseluruhan
                                <div class="small fw-normal text-muted">{{ $totalHariMasuk }} hari aktif ×
                                    {{ $kelas->siswa->count() }} siswa = {{ $rekapKelasTotal['slot'] }} slot</div>
                            </td>
                            <td class="text-center">{{ $rekapKelasTotal['hadir'] }}
                                <span class="fw-normal text-muted small">({{ $rekapKelasTotal['persen_hadir'] }}%)</span></td>
                            <td class="text-center">{{ $rekapKelasTotal['izin'] }}
                                <span class="fw-normal text-muted small">({{ $rekapKelasTotal['persen_izin'] }}%)</span></td>
                            <td class="text-center">{{ $rekapKelasTotal['sakit'] }}
                                <span class="fw-normal text-muted small">({{ $rekapKelasTotal['persen_sakit'] }}%)</span></td>
                            <td class="text-center">{{ $rekapKelasTotal['alpa'] }}
                                <span class="fw-normal text-muted small">({{ $rekapKelasTotal['persen_alpa'] }}%)</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-secondary small mt-3 mb-0 d-flex align-items-start gap-2">
            <i class="bi bi-info-circle mt-1"></i>
            <div>Persentase per siswa = hari Hadir ÷ hari ada presensi tercatat di bulan itu. Persentase keseluruhan kelas = jumlah kejadian status (Hadir/Izin/Sakit/Alpa) ÷ (hari aktif × jumlah siswa) — jadi contoh "20 kejadian Sakit" itu dihitung dari gabungan semua siswa di kelas, bukan per orang.</div>
        </div>
    @endif

@endsection
