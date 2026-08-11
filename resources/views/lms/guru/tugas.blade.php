@extends('lms.layouts.app')

@section('title', 'Tugas - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Tugas</p>
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
        <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-1"></i> Buat Tugas</div>
        <div class="card-body">
            <form method="POST" action="{{ route('lms.guru.tugas.store', $pengampuMapel) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Batas Waktu</label>
                        <input type="datetime-local" name="batas_waktu" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">File Lampiran (opsional)</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Deskripsi (opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-save me-1"></i> Simpan Tugas
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Daftar Tugas</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Batas Waktu</th>
                        <th>Terkumpul</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugas as $t)
                        <tr>
                            <td>
                                {{ $t->judul }}
                                @if ($t->sudah_lewat_batas_waktu)
                                    <span class="badge bg-secondary ms-1">Selesai</span>
                                @else
                                    <span class="badge bg-success ms-1">Aktif</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($t->batas_waktu)->translatedFormat('d M Y, H:i') }}</td>
                            <td>{{ $t->pengumpulan_count }} siswa</td>
                            <td class="text-end">
                                <a href="{{ route('lms.guru.tugas.kumpulan', $t) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-inbox"></i> Lihat & Nilai
                                </a>
                                <form method="POST" action="{{ route('lms.guru.tugas.destroy', $t) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus tugas ini beserta semua jawaban siswa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada tugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
