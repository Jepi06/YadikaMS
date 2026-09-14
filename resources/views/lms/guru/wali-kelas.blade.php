@extends('lms.layouts.app')

@section('title', 'Rekap Nilai Wali Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-clipboard-data me-1"></i> Rekap Nilai — Wali Kelas</h4>
            <p class="text-muted mb-0">{{ $kelas->nama_kelas }}</p>
        </div>
    </div>

    {{-- Filter kelas (kalau wali >1 kelas) + periode --}}
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                @if ($kelasDiwalikan->count() > 1)
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Kelas</label>
                        <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach ($kelasDiwalikan as $k)
                                <option value="{{ $k->id }}" @selected($k->id === $kelas->id)>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                @endif

                <div class="col-md-4">
                    <label class="form-label small mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($periodeList->pluck('tahun_ajaran')->unique() as $ta)
                            <option value="{{ $ta }}" @selected($ta === $tahunAjaran)>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach (['Ganjil', 'Genap'] as $s)
                            <option value="{{ $s }}" @selected($s === $semester)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    @if ($daftarPengampu->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                Belum ada mata pelajaran diajarkan di kelas ini untuk periode
                <strong>{{ $tahunAjaran }} {{ $semester }}</strong>.
            </div>
        </div>
    @else
        <p class="small text-muted mb-3">
            Menampilkan periode <strong>{{ $tahunAjaran }} — {{ $semester }}</strong>.
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
    @endif
@endsection
