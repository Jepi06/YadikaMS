@extends('lms.layouts.app')

@section('title', 'Modul Ajar - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Modul Ajar (Arsip Guru)</p>
        </div>
        <a href="{{ route('lms.guru.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="alert alert-secondary small">
        <i class="bi bi-shield-lock me-1"></i>
        Dokumen di sini <strong>tidak terlihat oleh siswa</strong> — cuma bisa diakses Anda dan Panel Super Admin (arsip admin/kepala sekolah).
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold"><i class="bi bi-cloud-upload me-1"></i> Unggah Modul Ajar</div>
        <div class="card-body">
            <form method="POST" action="{{ route('lms.guru.modul-ajar.store', $pengampuMapel) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">Judul</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: RPP Bab 3 - Semester Ganjil" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">File (maks 20MB)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Deskripsi (opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-save me-1"></i> Unggah
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Arsip Modul Ajar Saya</div>
        <div class="list-group list-group-flush">
            @forelse ($modulAjar as $m)
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">{{ $m->judul }}</h6>
                        @if ($m->deskripsi)
                            <p class="text-muted small mb-1">{{ $m->deskripsi }}</p>
                        @endif
                        <a href="{{ route('lms.file.modul-ajar', $m) }}" target="_blank" class="small">
                            <i class="bi bi-paperclip"></i> Lihat/Unduh File
                        </a>
                        <span class="text-muted small ms-2">{{ $m->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <form method="POST" action="{{ route('lms.guru.modul-ajar.destroy', $m) }}"
                          onsubmit="return confirm('Hapus modul ajar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">Belum ada modul ajar diunggah.</div>
            @endforelse
        </div>
    </div>
@endsection
