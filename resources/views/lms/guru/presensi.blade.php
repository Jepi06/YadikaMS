@extends('lms.layouts.app')

@section('title', 'Presensi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-1">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('lms.guru.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success mt-3">{{ session('status') }}</div>
    @endif

    <div class="row g-3 mt-1">
        {{-- Kolom kiri: kontrol sesi barcode --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-qr-code-scan me-1"></i> Presensi Barcode
                </div>
                <div class="card-body text-center">
                    @if ($sesi && $sesi->masih_aktif)
                        <span class="badge bg-success mb-3">Sesi Aktif</span>
                        <div id="qrcode" class="d-flex justify-content-center mb-3"></div>
                        <p class="small text-muted mb-3">
                            Tampilkan QR ini di layar/proyektor. Siswa scan pakai kamera HP
                            (harus sudah login akun LMS masing-masing).
                        </p>
                        <form method="POST" action="{{ route('lms.guru.presensi.tutup', $pengampuMapel) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-stop-circle me-1"></i> Tutup Sesi
                            </button>
                        </form>
                    @else
                        @if ($sesi)
                            <span class="badge bg-secondary mb-3">Sesi Ditutup</span>
                        @else
                            <span class="badge bg-light text-muted mb-3">Belum Ada Sesi Hari Ini</span>
                        @endif
                        <p class="small text-muted mb-3">
                            Buka sesi untuk menampilkan QR code presensi hari ini.
                        </p>
                        <form method="POST" action="{{ route('lms.guru.presensi.buka', $pengampuMapel) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-play-circle me-1"></i> Buka Sesi Presensi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom kanan: presensi manual per siswa --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pencil-square me-1"></i> Presensi Manual</span>
                    <span class="small text-muted">Otomatis terisi kalau siswa sudah scan barcode</span>
                </div>
                <form method="POST" action="{{ route('lms.guru.presensi.manual', $pengampuMapel) }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th style="width:160px">Status</th>
                                    <th>Keterangan</th>
                                    <th style="width:90px">Sumber</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengampuMapel->kelas->siswa as $siswa)
                                    @php $p = $presensiSiswa->get($siswa->id); @endphp
                                    <tr>
                                        <td>{{ $siswa->nama }}</td>
                                        <td>
                                            <select name="presensi[{{ $siswa->id }}][status]" class="form-select form-select-sm">
                                                @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $status)
                                                    <option value="{{ $status }}" @selected(($p->status ?? 'Alpa') === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="presensi[{{ $siswa->id }}][keterangan]"
                                                   value="{{ $p->keterangan ?? '' }}" class="form-control form-control-sm"
                                                   placeholder="Opsional">
                                        </td>
                                        <td>
                                            @if (($p->sumber ?? null) === 'barcode')
                                                <span class="badge bg-info-subtle text-info-emphasis">Barcode</span>
                                            @elseif ($p)
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Manual</span>
                                            @else
                                                <span class="badge bg-light text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($pengampuMapel->kelas->siswa->count())
                        <div class="p-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Presensi
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@if ($sesi && $sesi->masih_aktif)
    @push('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @endpush
    @push('scripts')
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: @json($scanUrl),
            width: 200,
            height: 200,
        });
    </script>
    @endpush
@endif
