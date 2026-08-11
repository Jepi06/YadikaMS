@extends('lms.layouts.app')

@section('title', 'Materi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">Materi &middot; {{ $pengampuMapel->guru->name ?? '-' }}</p>
        </div>
        <a href="{{ route('lms.siswa.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @forelse ($materi as $m)
                <div class="list-group-item">
                    <h6 class="mb-1">{{ $m->judul }}</h6>
                    @if ($m->deskripsi)
                        <p class="text-muted small mb-1">{{ $m->deskripsi }}</p>
                    @endif
                    @if ($m->file_path)
                        <a href="{{ route('lms.file.materi', $m) }}" target="_blank" class="small">
                            <i class="bi bi-paperclip"></i> Lihat/Unduh Lampiran
                        </a>
                    @endif
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">Belum ada materi untuk mata pelajaran ini.</div>
            @endforelse
        </div>
    </div>
@endsection
