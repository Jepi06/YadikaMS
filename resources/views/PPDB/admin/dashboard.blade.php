@extends('ppdb.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard PPDB')

@section('content')

{{-- Statistik Utama --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Pendaftar</div>
                    <div class="stat-value">{{ number_format($totalPendaftar) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Diterima</div>
                    <div class="stat-value">{{ number_format($totalDiterima) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-yellow">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Menunggu</div>
                    <div class="stat-value">{{ number_format($totalPending) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-red">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Ditolak</div>
                    <div class="stat-value">{{ number_format($totalDitolak) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Rekap Per Jurusan --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Rekap Penerimaan Per Jurusan</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('ppdb.export.pdf') }}" class="btn btn-sm btn-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </a>
                    <a href="{{ route('ppdb.export.excel') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                @foreach($rekapJurusan as $rekap)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-700" style="color:#1e3a8a;">{{ $rekap->kode }}</span>
                            <span class="text-muted ms-1" style="font-size:.82rem;">— {{ $rekap->nama }}</span>
                        </div>
                        <div style="font-size:.82rem;">
                            <span class="fw-700 text-success">{{ $rekap->diterima }}</span>
                            <span class="text-muted">/ {{ $rekap->kuota }}</span>
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar"
                             style="width: {{ $rekap->kuota > 0 ? round(($rekap->diterima / $rekap->kuota) * 100) : 0 }}%">
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-1" style="font-size:.78rem;color:#64748b;">
                        <span>Diterima: <b class="text-success">{{ $rekap->diterima }}</b></span>
                        <span>Pending: <b class="text-warning">{{ $rekap->pending }}</b></span>
                        <span>Ditolak: <b class="text-danger">{{ $rekap->ditolak }}</b></span>
                        <span>Sisa Kuota: <b>{{ $rekap->kuota - $rekap->diterima }}</b></span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pendaftar Terbaru --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-clock-history me-2 text-primary"></i>Pendaftar Terbaru
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendaftarTerbaru as $p)
                    <li class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-600" style="font-size:.9rem;">{{ $p->nama_lengkap }}</div>
                                <div class="text-muted" style="font-size:.78rem;">
                                    {{ $p->jurusan->kode ?? '-' }} · {{ $p->asal_sekolah }}
                                </div>
                            </div>
                            <span class="status-badge {{ $p->status_badge }}">
                                {{ $p->status_label }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-5">
                        Belum ada pendaftar
                    </li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="{{ route('ppdb.pendaftar.index') }}" class="btn btn-sm btn-primary">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
