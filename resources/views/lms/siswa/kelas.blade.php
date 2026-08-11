@extends('lms.layouts.app')

@section('title', 'Kelas Saya')

@section('content')
    <h4 class="fw-bold mb-1">Kelas Saya</h4>
    <p class="text-muted mb-4">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>

    <div class="row g-3">
        @forelse ($mapelDiKelas as $p)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $p->mataPelajaran->nama ?? '-' }}</h6>
                        <p class="text-muted small mb-3">
                            {{ $p->guru->name ?? '-' }} &middot; {{ $p->semester }} {{ $p->tahun_ajaran }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('lms.siswa.materi.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-journal-text"></i> Materi
                            </a>
                            <a href="{{ route('lms.siswa.tugas.index', $p) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-clipboard-check"></i> Tugas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        Belum ada mata pelajaran terdaftar untuk kelas Anda.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
