@extends('lms.layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
    <h4 class="fw-bold mb-1">Halo, {{ $lmsUser->name }} 👋</h4>
    <p class="text-muted mb-4">Berikut kelas yang Anda ampu.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-door-open-fill text-primary fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $kelasMengajar->count() }}</h3>
                    <small class="text-muted">Kelas Diampu</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-clipboard-x-fill text-danger fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $tugasBelumDinilai }}</h3>
                    <small class="text-muted">Tugas Belum Dinilai</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Kelas yang Diampu</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelasMengajar as $p)
                        <tr>
                            <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                            <td>{{ $p->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $p->kelas->siswa->count() ?? 0 }}</td>
                            <td>{{ $p->tahun_ajaran }}</td>
                            <td>{{ $p->semester }}</td>
                            <td>
                                <a href="{{ route('lms.guru.presensi.index', $p) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-qr-code-scan"></i> Presensi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Anda belum ditugaskan mengajar kelas apa
                                pun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Klik <strong>Presensi</strong> di tiap baris kelas untuk buka sesi QR / input manual.
        Fitur <strong>materi</strong> dan <strong>tugas</strong> akan menyusul di tahap berikutnya.
    </div>
@endsection
