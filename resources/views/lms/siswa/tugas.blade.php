@extends('lms.layouts.app')

@section('title', 'Tugas - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">Daftar Tugas</p>
        </div>
        <a href="{{ route('lms.siswa.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Batas Waktu</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugas as $t)
                        @php $p = $t->pengumpulan->first(); @endphp
                        <tr>
                            <td>{{ $t->judul }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->batas_waktu)->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                @if ($p?->dikumpulkan_at)
                                    <span class="badge bg-success-subtle text-success-emphasis">Sudah Dikumpulkan</span>
                                @elseif ($t->sudah_lewat_batas_waktu)
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Lewat Batas Waktu</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Belum Dikumpulkan</span>
                                @endif
                            </td>
                            <td>{{ $p?->nilai ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('lms.siswa.tugas.show', $t) }}" class="btn btn-sm btn-outline-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada tugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
