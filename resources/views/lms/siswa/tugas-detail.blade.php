@extends('lms.layouts.app')

@section('title', $tugas->judul)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $tugas->judul }}</h4>
            <p class="text-muted mb-0">{{ $tugas->pengampuMapel->mataPelajaran->nama ?? '-' }}</p>
        </div>
        <a href="{{ route('lms.siswa.tugas.index', $tugas->pengampuMapel) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="mb-2"><strong>Batas Waktu:</strong>
                {{ \Carbon\Carbon::parse($tugas->batas_waktu)->translatedFormat('l, d F Y, H:i') }}</p>
            @if ($tugas->deskripsi)
                <p class="mb-2">{{ $tugas->deskripsi }}</p>
            @endif
            @if ($tugas->file_lampiran)
                <a href="{{ route('lms.file.tugas', $tugas) }}" target="_blank">
                    <i class="bi bi-paperclip"></i> Lihat Lampiran Tugas
                </a>
            @endif
        </div>
    </div>

    @if ($pengumpulanSaya?->sudah_dinilai)
        <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill"></i> Sudah Dinilai</h6>
                <p class="mb-1">Nilai: <strong>{{ $pengumpulanSaya->nilai }}</strong></p>
                @if ($pengumpulanSaya->catatan_guru)
                    <p class="mb-0 text-muted">Catatan guru: {{ $pengumpulanSaya->catatan_guru }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            {{ $pengumpulanSaya?->dikumpulkan_at ? 'Kumpulkan Ulang Jawaban' : 'Kumpulkan Jawaban' }}
        </div>
        <div class="card-body">
            @if ($pengumpulanSaya?->file_jawaban)
                <p class="small mb-3">
                    File sebelumnya:
                    <a href="{{ route('lms.file.jawaban', $pengumpulanSaya) }}" target="_blank">
                        lihat file yang sudah dikumpulkan
                    </a>
                    ({{ $pengumpulanSaya->dikumpulkan_at->translatedFormat('d M Y, H:i') }})
                </p>
            @endif

            <form method="POST" action="{{ route('lms.siswa.tugas.kumpul', $tugas) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">File Jawaban (maks 10MB)</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Catatan (opsional)</label>
                    <textarea name="catatan_siswa" class="form-control" rows="2">{{ $pengumpulanSaya->catatan_siswa ?? '' }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload me-1"></i> Kumpulkan
                </button>
            </form>
        </div>
    </div>
@endsection
