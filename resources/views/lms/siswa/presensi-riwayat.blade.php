@extends('lms.layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
    <h4 class="fw-bold mb-4">Riwayat Presensi</h4>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Status</th>
                        <th>Sumber</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ $r->tanggal->translatedFormat('d M Y') }}</td>
                            <td>{{ $r->pengampuMapel->mataPelajaran->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $badge = match($r->status) {
                                        'Hadir' => 'success',
                                        'Izin' => 'info',
                                        'Sakit' => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $r->status }}</span>
                            </td>
                            <td>
                                @if ($r->sumber === 'barcode')
                                    <span class="badge bg-info-subtle text-info-emphasis">Barcode</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Manual</span>
                                @endif
                            </td>
                            <td>{{ $r->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat presensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages())
            <div class="p-3 border-top">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
@endsection
