@extends('lms.layouts.app')

@section('title', 'Rekap Nilai Wali Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-clipboard-data me-1"></i> Rekap Nilai — Wali Kelas</h4>
            <p class="text-muted mb-0">{{ $kelas->nama_kelas }}</p>
        </div>

        @if ($kelasDiwalikan->count() > 1)
            <form method="GET" class="d-flex gap-2">
                <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach ($kelasDiwalikan as $k)
                        <option value="{{ $k->id }}" @selected($k->id === $kelas->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <p class="small text-muted mb-4">
        Nilai Akhir tiap mata pelajaran dihitung dari bobot yang diatur masing-masing guru pengampu.
    </p>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="position:sticky;left:0;background:#f8f9fa;">Siswa</th>
                        @foreach ($daftarPengampu as $p)
                            <th class="text-center">
                                {{ $p->mataPelajaran->nama ?? '-' }}
                                <div class="small fw-normal text-muted">{{ $p->guru->name ?? '-' }}</div>
                            </th>
                        @endforeach
                        <th class="text-center">Rata-rata Rapor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas->siswa as $siswa)
                        <tr>
                            <td style="position:sticky;left:0;background:#fff;">{{ $siswa->nama }}</td>
                            @php $totalAkhir = 0; $jumlahMapel = 0; @endphp
                            @foreach ($daftarPengampu as $p)
                                @php $r = $rekapPerMapel[$p->id]->get($siswa->id); @endphp
                                <td class="text-center">
                                    @if ($r)
                                        <span class="badge {{ $r->lengkap ? 'bg-dark' : 'bg-warning-subtle text-warning-emphasis' }}">
                                            {{ $r->nilai_akhir }}
                                        </span>
                                        @php $totalAkhir += $r->nilai_akhir; $jumlahMapel++; @endphp
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-center fw-bold">
                                {{ $jumlahMapel ? number_format($totalAkhir / $jumlahMapel, 1) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $daftarPengampu->count() + 2 }}" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-secondary small mt-3 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Badge kuning = ada komponen nilai yang belum lengkap (STS/SAS/Sikap/Tugas) di mata pelajaran itu.
    </div>
@endsection
