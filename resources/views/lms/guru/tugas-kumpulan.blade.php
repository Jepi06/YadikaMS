@extends('lms.layouts.app')

@section('title', 'Nilai Tugas - ' . $tugas->judul)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $tugas->judul }}</h4>
            <p class="text-muted mb-0">
                {{ $tugas->pengampuMapel->mataPelajaran->nama ?? '-' }} &middot;
                {{ $tugas->pengampuMapel->kelas->nama_kelas ?? '-' }} &middot;
                Batas: {{ \Carbon\Carbon::parse($tugas->batas_waktu)->translatedFormat('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('lms.guru.tugas.index', $tugas->pengampuMapel) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Siswa</th>
                        <th>Status</th>
                        <th>Jawaban</th>
                        <th style="width:110px">Nilai</th>
                        <th>Catatan Guru</th>
                        <th style="width:90px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugas->pengampuMapel->kelas->siswa as $siswa)
                        @php $p = $pengumpulan->get($siswa->id); @endphp
                        <tr>
                            <td>{{ $siswa->nama }}</td>
                            <td>
                                @if (!$p)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum Kumpul</span>
                                @elseif ($p->sudah_dinilai)
                                    <span class="badge bg-success-subtle text-success-emphasis">Sudah Dinilai</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Menunggu Dinilai</span>
                                @endif
                            </td>
                            <td>
                                @if ($p?->file_jawaban)
                                    <a href="{{ route('lms.file.jawaban', $p) }}" target="_blank">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                @if ($p?->catatan_siswa)
                                    <div class="small text-muted">{{ $p->catatan_siswa }}</div>
                                @endif
                            </td>

                            @if ($p)
                                <form method="POST" action="{{ route('lms.guru.tugas.kumpulan.nilai', $p) }}"
                                    style="display:contents">
                                    @csrf
                                    <td>
                                        <input type="number" name="nilai" min="0" max="100" step="0.1"
                                            value="{{ old('nilai', $p->nilai) }}" class="form-control form-control-sm"
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" name="catatan_guru"
                                            value="{{ old('catatan_guru', $p->catatan_guru) }}"
                                            class="form-control form-control-sm" placeholder="Opsional">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </td>
                                </form>
                            @else
                                <td colspan="3" class="text-muted small">Menunggu siswa mengumpulkan.</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
