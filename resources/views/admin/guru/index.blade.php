@extends('admin.layouts.app')

@section('title', 'Kelola Guru')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Kelola Guru</h4>
            <p class="text-muted mb-0 small">Periode aktif: {{ $tahunAjaran }} — {{ $semester }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.import.form') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </a>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Mengajar (periode aktif)</th>
                        <th>Wali Kelas</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guru as $g)
                        @php $wali = $waliKelasMap->get($g->id); @endphp
                        <tr>
                            <td>{{ $g->name }}</td>
                            <td class="small text-muted">{{ $g->email }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">{{ $g->mengajar_aktif_count }} kelas</span>
                            </td>
                            <td>
                                @if ($wali)
                                    <span class="badge bg-success-subtle text-success-emphasis">{{ $wali->kelas->nama_kelas ?? '-' }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.guru.kelola', $g) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-sliders"></i> Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada guru LMS terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($guru->hasPages())
            <div class="p-3 border-top">{{ $guru->links() }}</div>
        @endif
    </div>
@endsection
