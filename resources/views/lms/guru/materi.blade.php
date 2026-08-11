@extends('lms.layouts.app')

@section('title', 'Materi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Materi</p>
        </div>
        <a href="{{ route('lms.guru.dashboard') }}" class="btn btn-sm btn-outline-secondary">
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
        <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-1"></i> Tambah Materi</div>
        <div class="card-body">
            <form method="POST" action="{{ route('lms.guru.materi.store', $pengampuMapel) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">File (opsional, maks 10MB)</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Deskripsi (opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-save me-1"></i> Simpan Materi
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Daftar Materi</div>
        <div class="list-group list-group-flush">
            @forelse ($materi as $m)
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
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
                    <form method="POST" action="{{ route('lms.guru.materi.destroy', $m) }}"
                        onsubmit="return confirm('Hapus materi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">Belum ada materi.</div>
            @endforelse
        </div>
    </div>
@endsection
