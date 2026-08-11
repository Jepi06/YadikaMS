@extends('lms.layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <h4 class="fw-bold mb-1">Halo, {{ $lmsUser->name }} 👋</h4>
    <p class="text-muted mb-3">
        Kelas: <strong>{{ $siswa->kelas->nama_kelas ?? '-' }}</strong>
        &middot;
        <a href="{{ route('lms.siswa.presensi.riwayat') }}"><i class="bi bi-calendar-check"></i> Lihat Riwayat Presensi</a>
    </p>

    <a href="{{ route('lms.siswa.presensi.kamera') }}" class="btn btn-primary btn-lg mb-4">
        <i class="bi bi-qr-code-scan me-1"></i> Presensi Sekarang
    </a>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-journal-bookmark-fill text-primary fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $mapelDiKelas->count() }}</h3>
                    <small class="text-muted">Mata Pelajaran</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-clipboard-x-fill text-danger fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $tugasBelumDikumpulkan }}</h3>
                    <small class="text-muted">Tugas Belum Dikumpulkan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Mata Pelajaran di Kelas Anda</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mapelDiKelas as $p)
                        <tr>
                            <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                            <td>{{ $p->guru->name ?? '-' }}</td>
                            <td>{{ $p->semester }} &middot; {{ $p->tahun_ajaran }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada mata pelajaran terdaftar untuk
                                kelas Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Klik <strong>Presensi Sekarang</strong> untuk buka kamera dan scan QR dari guru.
        Materi & tugas per mata pelajaran bisa dibuka lewat menu <strong>Kelas Saya</strong>.
    </div>
@endsection
