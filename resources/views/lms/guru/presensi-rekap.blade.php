@extends('lms.layouts.app')

@section('title', 'Rekap Presensi')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Rekap Presensi</h4>
            <p class="text-muted mb-0">
                {{ $pengampuMapel->mataPelajaran->nama ?? '-' }} &middot; {{ $pengampuMapel->kelas->nama_kelas ?? '-' }}
            </p>
        </div>
        <a href="{{ route('lms.guru.presensi.index', $pengampuMapel) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Presensi
        </a>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <form method="GET" action="{{ route('lms.guru.presensi.rekap', $pengampuMapel) }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-1">Dari</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Sampai</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                </div>
                <div class="col-auto ms-auto text-muted small">
                    Total pertemuan tercatat: <strong>{{ $totalPertemuan }}</strong>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Siswa</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Alpa</th>
                        <th class="text-center" style="width:160px">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap as $r)
                        <tr>
                            <td>{{ $r->siswa->nama }}</td>
                            <td class="text-center">{{ $r->hadir }}</td>
                            <td class="text-center">{{ $r->izin }}</td>
                            <td class="text-center">{{ $r->sakit }}</td>
                            <td class="text-center">
                                @if ($r->alpa > 0)
                                    <span class="text-danger fw-semibold">{{ $r->alpa }}</span>
                                @else
                                    {{ $r->alpa }}
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px">
                                        <div class="progress-bar {{ $r->persentase < 75 ? 'bg-danger' : 'bg-success' }}"
                                             style="width:{{ $r->persentase }}%"></div>
                                    </div>
                                    <span class="small">{{ $r->persentase }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data presensi di rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
