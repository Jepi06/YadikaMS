@extends('lms.layouts.app')

@section('title', 'Materi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Materi</p>
        </div>
        <a href="{{ route('lms.guru.kelas.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <div class="col-md-6">
                        <label class="form-label small">Link (opsional)</label>
                        <input type="url" name="link_url" class="form-control" placeholder="https://youtube.com/..."
                            value="{{ old('link_url') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Deskripsi (opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Akses Materi</label>
                        <select name="mode_akses" class="form-select">
                            <option value="bebas">Bebas — langsung terbuka</option>
                            <option value="berurutan">Berurutan — buka setelah materi sebelumnya selesai</option>
                            <option value="manual">Dibuka Guru — terkunci sampai saya buka</option>
                            <option value="tanggal">Terjadwal — buka otomatis di tanggal tertentu</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Tanggal Buka <span class="text-muted">(khusus mode
                                Terjadwal)</span></label>
                        <input type="datetime-local" name="buka_pada" class="form-control">
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
                        @if ($m->link_url)
                            <div>
                                <a href="{{ $m->link_url }}" target="_blank" rel="noopener noreferrer" class="small">
                                    <i class="bi bi-link-45deg"></i> {{ \Illuminate\Support\Str::limit($m->link_url, 60) }}
                                </a>
                            </div>
                        @endif
                        <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                            @php
                                $labelMode =
                                    [
                                        'bebas' => 'Bebas',
                                        'berurutan' => 'Berurutan',
                                        'manual' => 'Dibuka Guru',
                                        'tanggal' => 'Terjadwal',
                                    ][$m->mode_akses] ?? 'Bebas';
                            @endphp
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                <i class="bi bi-lock"></i> {{ $labelMode }}
                                @if ($m->mode_akses === 'tanggal' && $m->buka_pada)
                                    · {{ $m->buka_pada->translatedFormat('d M Y H:i') }}
                                @endif
                            </span>

                            @if ($m->mode_akses === 'manual')
                                <form method="POST" action="{{ route('lms.guru.materi.toggle', $m) }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $m->dibuka_manual ? 'btn-outline-danger' : 'btn-success' }}">
                                        {{ $m->dibuka_manual ? 'Kunci Lagi' : 'Buka Sekarang' }}
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('lms.guru.materi.akses', $m) }}"
                                class="d-flex flex-wrap gap-1">
                                @csrf
                                @method('PUT')
                                <select name="mode_akses" class="form-select form-select-sm" style="width:auto">
                                    @foreach (['bebas' => 'Bebas', 'berurutan' => 'Berurutan', 'manual' => 'Dibuka Guru', 'tanggal' => 'Terjadwal'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected($m->mode_akses === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="buka_pada" class="form-control form-control-sm"
                                    style="width:auto" value="{{ $m->buka_pada?->format('Y-m-d\TH:i') }}">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Ubah</button>
                            </form>
                        </div>
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
