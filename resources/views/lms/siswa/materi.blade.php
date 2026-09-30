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
                @php
                    $alasan = $statusAkses[$m->id] ?? null;
                    $sudahSelesai = in_array($m->id, $selesaiIds);
                @endphp
                <div class="list-group-item {{ $alasan ? 'bg-light' : '' }}">
                    <h6 class="mb-1">
                        @if ($alasan)
                            <i class="bi bi-lock-fill text-secondary me-1"></i>
                        @elseif ($sudahSelesai)
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                        @endif
                        {{ $m->judul }}
                    </h6>

                    @if ($alasan)
                        {{-- Terkunci: deskripsi & file disembunyikan --}}
                        <p class="small text-muted mb-0"><i class="bi bi-info-circle"></i> {{ $alasan }}</p>
                    @else
                        @if ($m->deskripsi)
                            <p class="text-muted small mb-1">{{ $m->deskripsi }}</p>
                        @endif
                        @if ($m->file_path)
                            <a href="{{ route('lms.file.materi', $m) }}" target="_blank" class="small">
                                <i class="bi bi-paperclip"></i> Lihat/Unduh Lampiran
                            </a>
                        @endif

                        @if ($sudahSelesai)
                            <div><span class="badge bg-success-subtle text-success-emphasis mt-2">Selesai dipelajari</span>
                            </div>
                        @else
                            <form method="POST" action="{{ route('lms.siswa.materi.selesai', $m) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-check2"></i> Tandai Selesai
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">Belum ada materi untuk mata pelajaran ini.</div>
            @endforelse
        </div>
    </div>
@endsection
