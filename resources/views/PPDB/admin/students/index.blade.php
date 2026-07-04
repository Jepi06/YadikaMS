@extends('ppdb.layouts.admin')

@section('title', 'Data Pendaftar')
@section('page-title', 'Data Pendaftar')

@section('content')

{{-- Jurusan Tabs --}}
<div class="mb-3">
    <ul class="nav nav-tabs" style="border-bottom: 2px solid #dbeafe;">
        <li class="nav-item">
            <a class="nav-link {{ !request('jurusan') ? 'active' : '' }}"
               href="{{ route('ppdb.pendaftar.index', array_filter(['status' => request('status'), 'search' => request('search')])) }}">
                <i class="bi bi-grid me-1"></i>Semua
            </a>
        </li>
        @foreach($jurusan as $j)
        <li class="nav-item">
            <a class="nav-link {{ request('jurusan') == $j->id ? 'active' : '' }}"
               href="{{ route('ppdb.pendaftar.index', array_filter(['jurusan' => $j->id, 'status' => request('status'), 'search' => request('search')])) }}">
                {{ $j->kode }}
                <span class="badge ms-1" style="background:rgba(255,255,255,.25);font-size:.7rem;">
                    {{ $stats->where('id', $j->id)->first()?->pendaftar_count ?? 0 }}
                </span>
            </a>
        </li>
        @endforeach
    </ul>
</div>

{{-- Filter & Export Bar --}}
<div class="filter-bar d-flex flex-wrap gap-3 align-items-end">
    <form action="{{ route('ppdb.pendaftar.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-end flex-grow-1">
        @if(request('jurusan'))
            <input type="hidden" name="jurusan" value="{{ request('jurusan') }}">
        @endif

        <div>
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:700;color:#475569;">Status</label>
            <select name="status" class="form-select form-select-sm" style="min-width:130px;border-radius:8px;">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak"  {{ request('status') === 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="flex-grow-1" style="min-width:200px;">
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:700;color:#475569;">Cari</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="border-radius:8px 0 0 8px;"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Nama / No. Pendaftaran / Sekolah"
                       value="{{ request('search') }}" style="border-radius:0 8px 8px 0;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:8px;height:32px;">
            Filter
        </button>
        <a href="{{ route('ppdb.pendaftar.index', request('jurusan') ? ['jurusan' => request('jurusan')] : []) }}"
           class="btn btn-outline-secondary btn-sm px-3" style="border-radius:8px;height:32px;">
            Reset
        </a>
    </form>

    {{-- Export Buttons --}}
    <div class="d-flex gap-2">
        <a href="{{ route('ppdb.export.excel', array_filter(['jurusan' => request('jurusan'), 'status' => request('status')])) }}"
           class="btn btn-success btn-sm" style="border-radius:8px;">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
            @if(request('jurusan'))
                <span class="badge bg-white text-success ms-1" style="font-size:.65rem;">
                    {{ $jurusan->find(request('jurusan'))?->kode }}
                </span>
            @endif
        </a>
        <a href="{{ route('ppdb.export.pdf', array_filter(['jurusan' => request('jurusan')])) }}"
           class="btn btn-danger btn-sm" target="_blank" style="border-radius:8px;">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF Laporan
        </a>
    </div>
</div>

{{-- Mini Stat Bar (untuk jurusan terpilih) --}}
@if(request('jurusan'))
    @php $selectedJurusan = $stats->where('id', request('jurusan'))->first(); @endphp
    @if($selectedJurusan)
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#dbeafe;">
                <div class="fw-800" style="font-size:1.5rem;color:#1e40af;">{{ $selectedJurusan->pendaftar_count }}</div>
                <div style="font-size:.75rem;color:#3b82f6;font-weight:600;">Total Pendaftar</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#d1fae5;">
                <div class="fw-800" style="font-size:1.5rem;color:#065f46;">{{ $selectedJurusan->diterima_count }}</div>
                <div style="font-size:.75rem;color:#059669;font-weight:600;">Diterima</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#fef3c7;">
                <div class="fw-800" style="font-size:1.5rem;color:#92400e;">{{ $selectedJurusan->pending_count }}</div>
                <div style="font-size:.75rem;color:#d97706;font-weight:600;">Menunggu</div>
            </div>
        </div>
    </div>
    @endif
@endif

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>
            <i class="bi bi-table me-2"></i>
            @if(request('jurusan'))
                {{ $jurusan->find(request('jurusan'))?->nama ?? 'Jurusan' }}
            @else
                Semua Pendaftar
            @endif
            <span class="badge ms-2" style="background:#dbeafe;color:#1e40af;">
                {{ $pendaftar->total() }} data
            </span>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>No. Daftar</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Asal Sekolah</th>
                        <th>Jurusan</th>
                        <th>No. HP</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $i => $p)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $pendaftar->firstItem() + $i }}</td>
                        <td>
                            <span class="font-mono" style="font-size:.78rem;color:#475569;">{{ $p->no_pendaftaran }}</span>
                        </td>
                        <td>
                            <div class="fw-600">{{ $p->nama_lengkap }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $p->agama }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $p->jenis_kelamin === 'L' ? '#dbeafe' : '#fce7f3' }};color:{{ $p->jenis_kelamin === 'L' ? '#1e40af' : '#9d174d' }};">
                                {{ $p->jenis_kelamin_label }}
                            </span>
                        </td>
                        <td style="font-size:.85rem;">{{ $p->asal_sekolah }}</td>
                        <td>
                            <span class="badge" style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd;font-size:.75rem;">
                                {{ $p->jurusan->kode ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ $p->whatsapp_url }}" target="_blank" class="btn btn-wa btn-sm" style="border-radius:8px;font-size:.75rem;padding:4px 10px;">
                                <i class="bi bi-whatsapp me-1"></i>{{ $p->no_hp }}
                            </a>
                        </td>
                        <td style="font-size:.82rem;">
                            @if($p->nominal_pembayaran > 0)
                                <span class="fw-600 {{ $p->nominal_pembayaran >= 2500000 ? 'text-success' : 'text-danger' }}">
                                    {{ $p->nominal_formatted }}
                                </span>
                            @else
                                <span class="text-muted">Belum</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $p->status_badge }}">
                                {{ $p->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('ppdb.pendaftar.show', $p) }}" class="btn btn-primary btn-sm" style="border-radius:8px;font-size:.75rem;">
                                <i class="bi bi-pencil-square me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Tidak ada data pendaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pendaftar->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="text-muted" style="font-size:.83rem;">
            Menampilkan {{ $pendaftar->firstItem() }}–{{ $pendaftar->lastItem() }} dari {{ $pendaftar->total() }}
        </span>
        {{ $pendaftar->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
