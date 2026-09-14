@extends('lms.layouts.app')

@section('title', 'Kelas Saya')

@section('content')
    <h4 class="fw-bold mb-4">Kelas Saya</h4>

    @php
        // Kelompokkan per "Tahun Ajaran - Semester", urut dari periode
        // TERBARU ke paling lama, biar semester sekarang selalu di atas.
        $bobotSemester = ['Genap' => 2, 'Ganjil' => 1];

        $grup = $kelasMengajar
            ->groupBy(fn($p) => $p->tahun_ajaran . '||' . $p->semester)
            ->sortByDesc(function ($items, $key) use ($bobotSemester) {
                [$tahunAjaran, $semester] = explode('||', $key);
                // "2024/2025" -> 2024, digabung sama bobot semester biar
                // urutannya: tahun terbaru dulu, lalu Genap sebelum Ganjil.
                $tahunAngka = (int) substr($tahunAjaran, 0, 4);
                return $tahunAngka * 10 + ($bobotSemester[$semester] ?? 0);
            });
    @endphp

    @forelse ($grup as $key => $items)
        @php [$tahunAjaran, $semester] = explode('||', $key); @endphp

        <div class="d-flex align-items-center gap-2 mb-3 mt-4">
            <h6 class="fw-bold mb-0 text-primary">{{ $tahunAjaran }} — {{ $semester }}</h6>
            <span class="badge bg-primary-subtle text-primary">{{ $items->count() }} kelas</span>
        </div>

        <div class="row g-3">
            @foreach ($items as $p)
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $p->mataPelajaran->nama ?? '-' }}</h6>
                            <p class="text-muted small mb-3">
                                {{ $p->kelas->nama_kelas ?? '-' }} &middot;
                                {{ $p->kelas->siswa->count() ?? 0 }} siswa
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('lms.guru.materi.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-journal-text"></i> Materi
                                </a>
                                <a href="{{ route('lms.guru.tugas.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-clipboard-check"></i> Tugas
                                </a>
                                <a href="{{ route('lms.guru.nilai.index', $p) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-clipboard-data"></i> Nilai
                                </a>
                                <a href="{{ route('lms.guru.presensi.index', $p) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-qr-code-scan"></i> Presensi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                Anda belum ditugaskan mengajar kelas apa pun.
            </div>
        </div>
    @endforelse
@endsection
