@extends('lms.layouts.app')

@section('title', 'Tugas - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Tugas</p>
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

    {{-- Form Buat Tugas --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Buat Tugas
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('lms.guru.tugas.store', $pengampuMapel) }}"
                enctype="multipart/form-data">
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
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_kelompok" value="1"
                                class="form-check-input" id="isKelompok">
                            <label for="isKelompok" class="form-check-label">
                                Tugas Kelompok
                                <span class="text-muted small">(siswa bikin kelompok sendiri, ketua yang kumpulkan)</span>
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-save me-1"></i> Simpan Tugas
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Tugas --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Daftar Tugas</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Batas Waktu</th>
                        <th>Terkumpul</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugas as $t)
                        {{-- Baris utama --}}
                        <tr>
                            <td>
                                {{ $t->judul }}
                                @if ($t->alasanTidakBisaKumpul())
                                    <span class="badge bg-secondary ms-1"
                                        title="{{ $t->alasanTidakBisaKumpul() }}">
                                        Tertutup
                                    </span>
                                @else
                                    <span class="badge bg-success ms-1">Terbuka</span>
                                @endif
                                @if ($t->is_kelompok)
                                    <span class="badge bg-info-subtle text-info ms-1">Kelompok</span>
                                @endif
                            </td>
                            <td class="small">
                                {{ \Carbon\Carbon::parse($t->batas_waktu)->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td>{{ $t->pengumpulan_count }} siswa</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-dark"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#edit{{ $t->id }}">
                                        <i class="bi bi-gear"></i> Atur
                                    </button>
                                    <a href="{{ route('lms.guru.tugas.kumpulan', $t) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-inbox"></i> Lihat & Nilai
                                    </a>
                                    <form method="POST"
                                        action="{{ route('lms.guru.tugas.destroy', $t) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Hapus tugas ini beserta semua jawaban siswa?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Baris collapse Atur --}}
                        <tr id="edit{{ $t->id }}" class="collapse">
                            <td colspan="4" class="bg-light p-3">
                                {{-- Form edit judul/deadline/mode --}}
                                <form method="POST"
                                    action="{{ route('lms.guru.tugas.update', $t) }}"
                                    class="row g-2 align-items-end mb-3">
                                    @csrf @method('PUT')

                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Judul</label>
                                        <input type="text" name="judul"
                                            class="form-control form-control-sm"
                                            value="{{ $t->judul }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Batas Waktu (Deadline)</label>
                                        <input type="datetime-local" name="batas_waktu"
                                            class="form-control form-control-sm"
                                            value="{{ $t->batas_waktu->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-semibold">Mode Buka</label>
                                        <select name="mode_buka" class="form-select form-select-sm"
                                            onchange="toggleMulaiPada(this, {{ $t->id }})">
                                            <option value="bebas"    @selected($t->mode_buka === 'bebas')>Bebas</option>
                                            <option value="manual"   @selected($t->mode_buka === 'manual')>Manual</option>
                                            <option value="tanggal"  @selected($t->mode_buka === 'tanggal')>Terjadwal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="wrap_mulai_{{ $t->id }}"
                                        style="{{ $t->mode_buka !== 'tanggal' ? 'display:none' : '' }}">
                                        <label class="form-label small fw-semibold">Mulai Pada</label>
                                        <input type="datetime-local" name="mulai_pada"
                                            class="form-control form-control-sm"
                                            value="{{ $t->mulai_pada?->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            Simpan
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control form-control-sm"
                                            rows="2">{{ $t->deskripsi }}</textarea>
                                    </div>
                                </form>

                                {{-- Tombol buka/tutup --}}
                                <div class="d-flex gap-2 flex-wrap">
                                    @if ($t->mode_buka === 'manual')
                                        <form method="POST"
                                            action="{{ route('lms.guru.tugas.toggle-buka', $t) }}">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $t->dibuka_manual ? 'btn-outline-danger' : 'btn-success' }}">
                                                <i class="bi bi-{{ $t->dibuka_manual ? 'lock' : 'unlock' }} me-1"></i>
                                                {{ $t->dibuka_manual ? 'Kunci Lagi' : 'Buka Sekarang' }}
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST"
                                        action="{{ route('lms.guru.tugas.toggle-tutup', $t) }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $t->ditutup_manual ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                            <i class="bi bi-{{ $t->ditutup_manual ? 'door-open' : 'door-closed' }} me-1"></i>
                                            {{ $t->ditutup_manual ? 'Buka Lagi (Batalkan Tutup Paksa)' : 'Tutup Paksa Sekarang' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada tugas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Tampil/sembunyikan field "Mulai Pada" berdasar mode buka
    function toggleMulaiPada(select, id) {
        const wrap = document.getElementById('wrap_mulai_' + id);
        if (wrap) {
            wrap.style.display = select.value === 'tanggal' ? '' : 'none';
        }
    }
</script>
@endpush