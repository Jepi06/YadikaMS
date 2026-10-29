@extends('admin.layouts.app')

@php $edit = $jenis->exists; @endphp

@section('title', ($edit ? 'Ubah' : 'Tambah') . ' Pelanggaran / Prestasi')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">{{ $edit ? 'Ubah' : 'Tambah' }} Pelanggaran / Prestasi</h4>
        <a href="{{ route('admin.poin.index', ['tipe' => $jenis->tipe]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" class="card border-0 shadow-sm"
          action="{{ $edit ? route('admin.poin.update', $jenis) : route('admin.poin.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipe</label>
                <div class="btn-group d-flex" role="group" style="max-width:420px">
                    <input type="radio" class="btn-check" name="tipe" id="tipeP" value="pelanggaran" @checked(old('tipe', $jenis->tipe) === 'pelanggaran')>
                    <label class="btn btn-outline-danger" for="tipeP">− Pelanggaran (poin berkurang)</label>
                    <input type="radio" class="btn-check" name="tipe" id="tipeR" value="prestasi" @checked(old('tipe', $jenis->tipe) === 'prestasi')>
                    <label class="btn btn-outline-success" for="tipeR">+ Prestasi (poin bertambah)</label>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" list="daftarKategori" maxlength="50" required
                           class="form-control" value="{{ old('kategori', $jenis->kategori) }}"
                           placeholder="mis. Kehadiran, Kerapihan, Akademik & Lomba">
                    <datalist id="daftarKategori">
                        @foreach ($kategoriList as $k)<option value="{{ $k }}">@endforeach
                    </datalist>
                    <div class="form-text">Pilih yang sudah ada atau ketik kategori baru.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="poin">Poin</label>
                    <input type="number" id="poin" name="poin" min="1" max="500" required class="form-control"
                           value="{{ old('poin', $jenis->poin) }}">
                    <div class="form-text" id="infoPoin"></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="urutan">Urutan <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="number" id="urutan" name="urutan" min="0" class="form-control"
                           value="{{ old('urutan', $edit ? $jenis->urutan : '') }}" placeholder="otomatis">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" for="nama">Nama pelanggaran / prestasi</label>
                    <input type="text" id="nama" name="nama" maxlength="255" required class="form-control"
                           value="{{ old('nama', $jenis->nama) }}">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch" id="aktif" name="aktif" value="1"
                               @checked(old('aktif', $jenis->aktif ?? true))>
                        <label class="form-check-label" for="aktif">Aktif (tampil di form guru)</label>
                    </div>
                </div>
            </div>

            @if ($edit)
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    Perubahan nama/poin hanya berlaku untuk pencatatan <b>berikutnya</b>. Catatan yang sudah ada tidak berubah.
                </div>
            @endif
        </div>

        <div class="card-footer bg-white">
            <button class="btn btn-dark" type="submit"><i class="bi bi-save me-1"></i> Simpan</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const info = document.getElementById('infoPoin');
        const poin = document.getElementById('poin');
        function update() {
            const p = document.querySelector('input[name="tipe"]:checked').value === 'pelanggaran';
            info.textContent = p ? 'Mengurangi saldo siswa' : 'Menambah saldo siswa';
            info.className = 'form-text ' + (p ? 'text-danger' : 'text-success');
        }
        document.querySelectorAll('input[name="tipe"]').forEach(r => r.addEventListener('change', update));
        update();
    })();
</script>
@endpush
