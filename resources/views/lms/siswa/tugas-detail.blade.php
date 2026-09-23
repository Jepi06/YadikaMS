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
                @if ($tugas->is_kelompok && !$kelompokSaya)
                    {{-- Belum punya kelompok — form bikin kelompok --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">Bikin Kelompok</div>
                        <div class="card-body">
                            <p class="small text-muted">Ini tugas kelompok. Pilih anggota, kamu otomatis jadi ketua.</p>
                            <form method="POST" action="{{ route('lms.siswa.tugas.kelompok.buat', $tugas) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small">Nama Kelompok (opsional)</label>
                                    <input type="text" name="nama_kelompok" class="form-control">
                                </div>
                                <label class="form-label small">Pilih Anggota</label>
                                @foreach ($tugas->pengampuMapel->kelas->siswa as $temanSekelas)
                                    @if ($temanSekelas->id !== $siswa->id)
                                        <div class="form-check">
                                            <input type="checkbox" name="anggota[]" value="{{ $temanSekelas->id }}"
                                                class="form-check-input" id="a{{ $temanSekelas->id }}">
                                            <label class="form-check-label"
                                                for="a{{ $temanSekelas->id }}">{{ $temanSekelas->nama }}</label>
                                        </div>
                                    @endif
                                @endforeach
                                <button type="submit" class="btn btn-primary mt-3">Bikin Kelompok</button>
                            </form>
                        </div>
                    </div>
                @elseif ($tugas->is_kelompok && $kelompokSaya)
                    {{-- Sudah punya kelompok --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-semibold">
                            Kelompok {{ $kelompokSaya->nama_kelompok ?? '' }}
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Ketua:</strong> {{ $kelompokSaya->ketua->nama }}</p>
                            <p class="mb-0"><strong>Anggota:</strong>
                                {{ $kelompokSaya->anggota->pluck('nama')->implode(', ') }}
                            </p>
                        </div>
                    </div>

                    @if ($kelompokSaya->ketua_siswa_id === $siswa->id)
                        {{-- Ketua yang kumpulkan --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-semibold">Kumpulkan (sebagai Ketua)</div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('lms.siswa.tugas.kumpul', $tugas) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small">File Jawaban (opsional)</label>
                                        <input type="file" name="file" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Atau Link Jawaban (opsional)</label>
                                        <input type="url" name="link_jawaban" class="form-control"
                                            placeholder="https://...">
                                        <div class="form-text">Isi minimal salah satu: file atau link.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Catatan (opsional)</label>
                                        <textarea name="catatan_siswa" class="form-control" rows="2">{{ $pengumpulanSaya->catatan_siswa ?? '' }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload me-1"></i> Kumpulkan buat Kelompok
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">Menunggu ketua kelompok ({{ $kelompokSaya->ketua->nama }})
                            mengumpulkan tugas.</div>
                    @endif
                @else
                    {{-- Tugas individu biasa --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            {{ $pengumpulanSaya?->dikumpulkan_at ? 'Kumpulkan Ulang Jawaban' : 'Kumpulkan Jawaban' }}
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('lms.siswa.tugas.kumpul', $tugas) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small">File Jawaban (opsional)</label>
                                    <input type="file" name="file" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Atau Link Jawaban (opsional)</label>
                                    <input type="url" name="link_jawaban" class="form-control"
                                        value="{{ $pengumpulanSaya->link_jawaban ?? '' }}" placeholder="https://...">
                                    <div class="form-text">Isi minimal salah satu: file atau link.</div>
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
                @endif
        </div>
    @endsection
