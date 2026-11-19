@extends('spmb.layouts.admin')

@section('title', 'Dashboard Admin SPMB')

@section('content')

    {{-- Judul halaman --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Dashboard SPMB</h3>
            <p class="text-muted mb-0 small">Pantau rekapitulasi data pendaftaran siswa baru secara real-time dan akurat.</p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-calendar3 me-1 text-primary"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2"
                onclick="window.print()">
                <i class="bi bi-printer"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card card-primary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase stat-label">Total Pendaftar</span>
                    <div class="stat-icon-wrapper stat-icon-primary"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-dark lh-1">{{ $totalPendaftar }}</div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small">Siswa</span>
                </div>
                <div class="text-muted mt-2 stat-hint">
                    <i class="bi bi-arrow-up-right text-primary me-1"></i>Seluruh berkas terdaftar
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card card-success">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase stat-label">Diterima</span>
                    <div class="stat-icon-wrapper stat-icon-success"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-success lh-1">{{ $totalDiterima }}</div>
                    <span class="badge bg-success-subtle text-success rounded-pill small">Lulus</span>
                </div>
                <div class="text-muted mt-2 stat-hint">
                    <i class="bi bi-patch-check-fill text-success me-1"></i>Lolos verifikasi panitia
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card card-warning">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase stat-label">Menunggu</span>
                    <div class="stat-icon-wrapper stat-icon-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-warning lh-1">{{ $totalPending }}</div>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill small">Antrean</span>
                </div>
                <div class="text-muted mt-2 stat-hint">
                    <i class="bi bi-clock-history text-warning me-1"></i>Menunggu validasi berkas
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card card-danger">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase stat-label">Belum Diterima</span>
                    <div class="stat-icon-wrapper stat-icon-danger"><i class="bi bi-x-circle-fill"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-danger lh-1">{{ $totalDitolak }}</div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill small">Tidak Lulus</span>
                </div>
                <div class="text-muted mt-2 stat-hint">
                    <i class="bi bi-info-circle text-danger me-1"></i>Belum memenuhi kriteria
                </div>
            </div>
        </div>
    </div>

    {{-- Rekap per jurusan --}}
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard text-primary"></i>
                    Rekapitulasi per Jurusan
                </h5>
                <p class="text-muted small mb-0">Rincian kuota, jumlah peminat pendaftar, dan sisa daya tampung per kejuruan.</p>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-2">
                Total Keahlian: <strong>{{ count($jurusanList) }}</strong>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 32%;">Program Keahlian (Jurusan)</th>
                        <th class="text-center" style="width: 14%;">Kuota</th>
                        <th class="text-center" style="width: 16%;">Total Pendaftar</th>
                        <th class="text-center" style="width: 14%;">Diterima</th>
                        <th class="text-center" style="width: 14%;">Sisa Kuota</th>
                        <th class="text-end no-print" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jurusanList as $j)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-wrapper stat-icon-primary sm">
                                        <i class="bi bi-award-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $j->nama }}</div>
                                        <div class="text-muted" style="font-size: .75rem;">SMK Yadika Soreang</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-quota badge-quota-neutral">
                                    <i class="bi bi-box-seam me-1 text-muted"></i>{{ $j->kuota }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-quota badge-quota-primary">
                                    <i class="bi bi-person-lines-fill me-1"></i>{{ $j->pendaftar_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-quota badge-quota-success">
                                    <i class="bi bi-check2-circle me-1"></i>{{ $j->total_diterima }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-quota {{ $j->sisa_kuota <= 5 ? 'badge-quota-warning' : 'badge-quota-neutral' }}">
                                    <i class="bi bi-pie-chart me-1"></i>{{ $j->sisa_kuota }}
                                </span>
                            </td>
                            <td class="text-end no-print">
                                <a href="{{ route('spmb.admin.pendaftar.per-jurusan', $j) }}" class="btn-view"
                                    title="Lihat Pendaftar {{ $j->nama }}">
                                    <span>Lihat</span>
                                    <i class="bi bi-arrow-right-short fs-6"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection