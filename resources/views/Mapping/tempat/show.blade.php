{{-- resources/views/Mapping/tempat/show.blade.php --}}
@extends('Mapping.layouts.app')
@section('title', 'Detail Tempat PKL')
@section('page-title', 'Detail Tempat PKL')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-0">{{ $tempat->nama_tempat }}</h5>
            <small class="text-muted">{{ $tempat->bidang_usaha ?? 'Bidang usaha belum diisi' }}</small>
        </div>
        <a href="{{ route('tempat.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- ============ KOLOM KIRI: info + upload ============ --}}
        <div class="col-lg-5">

            {{-- Info tempat --}}
            <div class="card mb-3">
                <div class="card-header py-3"><i class="bi bi-building me-2 text-primary"></i>Informasi Tempat</div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-4 text-muted fw-normal">Alamat</dt>
                        <dd class="col-8">{{ $tempat->alamat }}</dd>

                        <dt class="col-4 text-muted fw-normal">Kontak</dt>
                        <dd class="col-8">{{ $tempat->nama_kontak ?? '-' }}</dd>

                        <dt class="col-4 text-muted fw-normal">No. Telp</dt>
                        <dd class="col-8">{{ $tempat->no_telp ?? '-' }}</dd>

                        <dt class="col-4 text-muted fw-normal">Kuota</dt>
                        <dd class="col-8">
                            {{ $tempat->kuota_maksimal === null ? 'Tanpa batas' : $tempat->kuota_maksimal . ' siswa' }}
                        </dd>

                        <dt class="col-4 text-muted fw-normal">Siswa PKL</dt>
                        <dd class="col-8 mb-0">{{ $tempat->penempatan_pkl_count }} siswa</dd>
                    </dl>
                </div>
            </div>

            {{-- Upload MOU --}}
            <div class="card">
                <div class="card-header py-3"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Unggah Arsip MOU</div>
                <div class="card-body">
                    <form action="{{ route('tempat.mou.store', $tempat) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Berkas MOU <span
                                    class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-sm"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <div class="form-text">PDF / DOC / DOCX / JPG / PNG, maksimal 10 MB.</div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Judul</label>
                                <input type="text" name="judul" class="form-control form-control-sm"
                                    placeholder="mis. MOU Kerja Sama PKL" value="{{ old('judul') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nomor MOU</label>
                                <input type="text" name="nomor_mou" class="form-control form-control-sm"
                                    value="{{ old('nomor_mou') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                    value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Berakhir</label>
                                <input type="date" name="tanggal_berakhir" class="form-control form-control-sm"
                                    value="{{ old('tanggal_berakhir') }}">
                                <div class="form-text">Kosongkan jika tanpa batas waktu.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Keterangan</label>
                                <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Opsional...">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_public"
                                value="1" id="uploadPublik" {{ old('is_public') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="uploadPublik">
                                Izinkan berkas ini dilihat publik
                            </label>
                        </div>
                        <div class="form-text mb-3">
                            Kalau tidak dicentang, perusahaan tetap tampil di halaman publik tetapi berkasnya
                            disembunyikan.
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-upload me-1"></i>Unggah Arsip
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============ KOLOM KANAN: daftar arsip ============ --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-folder2-open me-2 text-primary"></i>Arsip MOU</span>
                    <span class="badge bg-primary-subtle text-primary">{{ $mou->count() }} berkas</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>MOU</th>
                                <th>Periode</th>
                                <th>Berkas</th>
                                <th>Akses Publik</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mou as $m)
                                @php
                                    $badgeStatus = match ($m->status) {
                                        'aktif' => ['bg-success-subtle text-success', 'Aktif'],
                                        'berakhir' => ['bg-secondary-subtle text-secondary', 'Berakhir'],
                                        default => ['bg-info-subtle text-info', 'Tanpa batas'],
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $m->judul ?: 'MOU' }}</div>
                                        @if ($m->nomor_mou)
                                            <small class="text-muted font-monospace">{{ $m->nomor_mou }}</small>
                                        @endif
                                        @if ($m->keterangan)
                                            <div class="small text-muted">{{ $m->keterangan }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $m->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                            <span class="text-muted">s/d</span>
                                            {{ $m->tanggal_berakhir?->format('d/m/Y') ?? '∞' }}
                                        </small>
                                        <div><span class="badge {{ $badgeStatus[0] }}">{{ $badgeStatus[1] }}</span></div>
                                    </td>
                                    <td>
                                        <a href="{{ route('tempat.mou.berkas', $m) }}" target="_blank"
                                            class="text-decoration-none small">
                                            <i class="bi bi-file-earmark-text me-1"></i>{{ \Illuminate\Support\Str::limit($m->file_nama_asli, 24) }}
                                        </a>
                                        <div class="small text-muted">{{ $m->ukuran_label }}</div>
                                    </td>
                                    <td>
                                        <form action="{{ route('tempat.mou.publik', $m) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="is_public" value="0">
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="is_public" value="1" id="pub{{ $m->id }}"
                                                    @checked($m->is_public) onchange="this.form.submit()">
                                                <label class="form-check-label small" for="pub{{ $m->id }}">
                                                    {{ $m->is_public ? 'Publik' : 'Privat' }}
                                                </label>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('tempat.mou.destroy', $m) }}" method="POST"
                                            onsubmit="return confirm('Hapus arsip MOU ini beserta berkasnya?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Belum ada arsip MOU untuk tempat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="small text-muted mt-2 mb-0">
                <i class="bi bi-info-circle"></i>
                Perusahaan dengan minimal satu arsip MOU otomatis tampil di halaman publik
                <a href="{{ route('pkl.mou.public') }}" target="_blank">Mitra &amp; MOU</a>.
                Saklar <strong>Akses Publik</strong> hanya mengatur apakah berkasnya boleh dibuka pengunjung.
            </p>
        </div>
    </div>
@endsection