@extends('ppdb.layouts.admin')

@section('title', 'Detail Pendaftar')
@section('page-title', 'Detail Pendaftar')

@section('content')

<div class="mb-3">
    <a href="{{ route('ppdb.pendaftar.index', request()->only(['jurusan', 'status', 'search'])) }}"
       class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-4">
    {{-- Info Pendaftar --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-circle me-2 text-primary"></i>Data Pendaftar</span>
                <span class="status-badge {{ $pendaftar->status_badge }} fs-6">
                    {{ $pendaftar->status_label }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">No. Pendaftaran</label>
                        <div class="font-mono fw-600 mt-1" style="color:#1e40af;">{{ $pendaftar->no_pendaftaran }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Tanggal Daftar</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->created_at->isoFormat('D MMMM Y, HH:mm') }}</div>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Nama Lengkap</label>
                        <div class="fw-600 mt-1" style="font-size:1.05rem;">{{ $pendaftar->nama_lengkap }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Jenis Kelamin</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->jenis_kelamin_label }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Agama</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->agama }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Jurusan Pilihan</label>
                        <div class="fw-600 mt-1">
                            <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.85rem;">
                                {{ $pendaftar->jurusan->kode ?? '-' }}
                            </span>
                            {{ $pendaftar->jurusan->nama ?? '-' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Alamat</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->alamat }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Nama Orang Tua</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->nama_orang_tua }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Asal Sekolah</label>
                        <div class="fw-600 mt-1">{{ $pendaftar->asal_sekolah }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted" style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">No. HP / WhatsApp</label>
                        <div class="mt-1">
                            <a href="{{ $pendaftar->whatsapp_url }}" target="_blank" class="btn btn-wa btn-sm" style="border-radius:10px;">
                                <i class="bi bi-whatsapp me-2"></i>{{ $pendaftar->no_hp }}
                                <span class="ms-1" style="font-size:.72rem;opacity:.85;">— Chat WA</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Admin: Input Nominal & Status --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-check me-2 text-primary"></i>Verifikasi Admin
            </div>
            <div class="card-body">

                {{-- Info Aturan --}}
                <div class="p-3 rounded-3 mb-4" style="background:#eff6ff;border:1px solid #bfdbfe;">
                    <div class="fw-700" style="color:#1e40af;font-size:.85rem;">
                        <i class="bi bi-info-circle me-1"></i>Ketentuan Penerimaan
                    </div>
                    <div class="mt-1" style="font-size:.82rem;color:#3b82f6;">
                        Nominal pembayaran <strong>≥ Rp 2.500.000</strong> → Status <b>Diterima</b><br>
                        Nominal <strong>&lt; Rp 2.500.000</strong> → Status <b>Ditolak</b>
                    </div>
                </div>

                {{-- Current Status --}}
                @if($pendaftar->nominal_pembayaran > 0)
                <div class="p-3 rounded-3 mb-4 {{ $pendaftar->status === 'diterima' ? '' : '' }}"
                     style="background:{{ $pendaftar->status === 'diterima' ? '#ecfdf5' : '#fef2f2' }};border:1px solid {{ $pendaftar->status === 'diterima' ? '#a7f3d0' : '#fecaca' }};">
                    <div class="fw-700" style="font-size:.85rem;color:{{ $pendaftar->status === 'diterima' ? '#065f46' : '#991b1b' }};">
                        <i class="bi bi-{{ $pendaftar->status === 'diterima' ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                        {{ $pendaftar->status_label }}
                    </div>
                    <div style="font-size:.82rem;color:{{ $pendaftar->status === 'diterima' ? '#059669' : '#dc2626' }};">
                        Nominal: {{ $pendaftar->nominal_formatted }}<br>
                        Diproses: {{ $pendaftar->processed_at?->isoFormat('D MMM Y, HH:mm') ?? '-' }}<br>
                        Oleh: {{ $pendaftar->processor?->name ?? '-' }}
                    </div>
                </div>
                @endif

                {{-- Form Update Status --}}
                <form action="{{ route('ppdb.pendaftar.updateStatus', $pendaftar) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label fw-700" style="font-size:.85rem;">
                            Nominal Pembayaran <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-700" style="background:#f0f9ff;border-color:#bfdbfe;color:#1e40af;">Rp</span>
                            <input type="number"
                                   name="nominal_pembayaran"
                                   class="form-control @error('nominal_pembayaran') is-invalid @enderror"
                                   placeholder="2500000"
                                   value="{{ old('nominal_pembayaran', $pendaftar->nominal_pembayaran) }}"
                                   min="0"
                                   step="1000"
                                   required
                                   style="border-radius:0 10px 10px 0;">
                        </div>
                        @error('nominal_pembayaran')
                            <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                        <div id="nominalHint" class="mt-1" style="font-size:.78rem;"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-700" style="font-size:.85rem;">Catatan Admin</label>
                        <textarea name="catatan_admin"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Opsional..."
                                  style="border-radius:10px;">{{ old('catatan_admin', $pendaftar->catatan_admin) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-700" style="border-radius:12px;">
                        <i class="bi bi-check2-circle me-2"></i>Simpan & Tentukan Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const input = document.querySelector('input[name="nominal_pembayaran"]');
    const hint  = document.getElementById('nominalHint');
    const MIN   = 2500000;

    function updateHint() {
        const val = parseInt(input.value) || 0;
        const fmt = 'Rp ' + val.toLocaleString('id-ID');

        if (val === 0) {
            hint.innerHTML = '';
        } else if (val >= MIN) {
            hint.innerHTML = `<span style="color:#059669;font-weight:700;">✔ ${fmt} → Akan <u>DITERIMA</u></span>`;
        } else {
            const kurang = MIN - val;
            hint.innerHTML = `<span style="color:#dc2626;font-weight:700;">✘ ${fmt} → Akan <u>DITOLAK</u> (kurang Rp ${kurang.toLocaleString('id-ID')})</span>`;
        }
    }

    input.addEventListener('input', updateHint);
    updateHint();
</script>
@endpush

@endsection
