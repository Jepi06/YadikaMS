@extends('lms.layouts.app')

@section('title', 'Rekap Absensi Wali Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar-check me-1"></i> Rekap Absensi — Wali Kelas</h4>
            <p class="text-muted mb-0">{{ $kelas->nama_kelas }}</p>
        </div>
        <a href="{{ route('lms.guru.wali-kelas.index', ['kelas_id' => $kelas->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester]) }}"
            class="btn btn-sm btn-outline-primary">
            <i class="bi bi-clipboard-data"></i> Lihat Rekap Nilai
        </a>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                @if ($kelasDiwalikan->count() > 1)
                    <div class="col-md-4">
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

                <div class="col-md-4">
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

                <div class="col-md-4">
                    <label class="form-label small mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach (['Ganjil', 'Genap'] as $s)
                            <option value="{{ $s }}" @selected($s === $semester)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    @if ($hariMasukPerBulan->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                Belum ada data presensi untuk periode {{ $tahunAjaran }} {{ $semester }}.
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
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
                                <td style="position:sticky;left:0;background:#fff;">{{ $r->siswa->nama }}</td>
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
                                        <span
                                            class="badge bg-{{ $warna }}-subtle text-{{ $warna }}-emphasis">
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

        <h5 class="fw-bold mt-5 mb-3">Rekap Keseluruhan Kelas</h5>
        <div class="card border-0 shadow-sm">
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
                                <td>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y') }}
                                    <div class="small text-muted">{{ $d['hari_aktif'] }} hari aktif ×
                                        {{ $kelas->siswa->count() }} siswa = {{ $d['slot'] }} slot</div>
                                </td>
                                <td class="text-center">{{ $d['hadir'] }} <span
                                        class="text-muted small">({{ $d['persen_hadir'] }}%)</span></td>
                                <td class="text-center">{{ $d['izin'] }} <span
                                        class="text-muted small">({{ $d['persen_izin'] }}%)</span></td>
                                <td class="text-center">{{ $d['sakit'] }} <span
                                        class="text-muted small">({{ $d['persen_sakit'] }}%)</span></td>
                                <td class="text-center">{{ $d['alpa'] }} <span
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
                                ({{ $rekapKelasTotal['persen_hadir'] }}%)</td>
                            <td class="text-center">{{ $rekapKelasTotal['izin'] }}
                                ({{ $rekapKelasTotal['persen_izin'] }}%)</td>
                            <td class="text-center">{{ $rekapKelasTotal['sakit'] }}
                                ({{ $rekapKelasTotal['persen_sakit'] }}%)</td>
                            <td class="text-center">{{ $rekapKelasTotal['alpa'] }}
                                ({{ $rekapKelasTotal['persen_alpa'] }}%)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-secondary small mt-3 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Persentase per siswa = hari Hadir ÷ hari ada presensi tercatat di bulan itu.
            Persentase keseluruhan kelas = jumlah kejadian status (Hadir/Izin/Sakit/Alpa) ÷ (hari aktif × jumlah siswa)
            — jadi contoh "20 kejadian Sakit" itu dihitung dari gabungan semua siswa di kelas, bukan per orang.
        </div>
    @endif

@endsection
