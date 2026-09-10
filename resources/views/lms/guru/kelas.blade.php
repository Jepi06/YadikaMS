@extends('lms.layouts.app')

@section('title', 'Kelas Saya')

@section('content')
    <h4 class="fw-bold mb-4">Kelas Saya</h4>

    <div class="row g-3">
        @forelse ($kelasMengajar as $p)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $p->mataPelajaran->nama ?? '-' }}</h6>
                        <p class="text-muted small mb-3">
                            {{ $p->kelas->nama_kelas ?? '-' }} &middot;
                            {{ $p->kelas->siswa->count() ?? 0 }} siswa &middot;
                            {{ $p->semester }} {{ $p->tahun_ajaran }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('lms.guru.materi.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-journal-text"></i> Materi
                            </a>
                            <a href="{{ route('lms.guru.tugas.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clipboard-check"></i> Tugas
                            </a>
                            <a href="{{ route('lms.guru.presensi.index', $p) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-qr-code-scan"></i> Presensi
                            </a>
                            <a href="{{ route('lms.guru.nilai.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clipboard-data"></i> Rekap Nilai
                            </a>
                            <a href="{{ route('lms.guru.modul-ajar.index', $p) }}" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-archive"></i> Modul Ajar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        Anda belum ditugaskan mengajar kelas apa pun.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
