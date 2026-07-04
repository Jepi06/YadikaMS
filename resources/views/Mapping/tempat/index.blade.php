@extends('Mapping.layouts.app')
@section('title', 'Tempat PKL')
@section('page-title', 'Data Tempat PKL')

@section('content')
<div class="card">
    <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="bi bi-building me-2 text-primary"></i>Daftar Tempat PKL</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTempat">
            <i class="bi bi-plus-lg me-1"></i>Tambah Tempat
        </button>
    </div>
    <div class="card-body border-bottom py-2">
        <form method="GET" class="row g-2">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / bidang usaha..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('tempat.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Nama Tempat</th><th>Bidang Usaha</th><th>Alamat</th><th>Kontak</th><th>No. Telp</th><th>Jml Siswa</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($tempat as $i => $t)
                    <tr>
                        <td>{{ $tempat->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $t->nama_tempat }}</td>
                        <td>{{ $t->bidang_usaha ?? '-' }}</td>
                        <td><small>{{ $t->alamat }}</small></td>
                        <td>{{ $t->nama_kontak ?? '-' }}</td>
                        <td>{{ $t->no_telp ?? '-' }}</td>
                        <td><span class="badge bg-info-subtle text-info">{{ $t->penempatan_pkl_count }} siswa</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditTempat{{ $t->id }}"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusTempat{{ $t->id }}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditTempat{{ $t->id }}" tabindex="-1">
                        <div class="modal-dialog"><form action="{{ route('tempat.update', $t) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header"><h6 class="modal-title fw-semibold">Edit Tempat PKL</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">@include('Mapping.tempat._form', ['tempat' => $t])</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form></div>
                    </div>

                    <div class="modal fade" id="modalHapusTempat{{ $t->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm"><form action="{{ route('tempat.destroy', $t) }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="modal-content">
                                <div class="modal-header border-0"><h6 class="modal-title fw-semibold text-danger">Hapus Tempat PKL</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body pt-0">Hapus <strong>{{ $t->nama_tempat }}</strong>?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </div>
                            </div>
                        </form></div>
                    </div>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">Belum ada data tempat PKL.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tempat->hasPages())<div class="card-footer">{{ $tempat->links() }}</div>@endif
</div>

<div class="modal fade" id="modalTambahTempat" tabindex="-1">
    <div class="modal-dialog"><form action="{{ route('tempat.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title fw-semibold">Tambah Tempat PKL</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('Mapping.tempat._form', ['tempat' => null])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </div>
    </form></div>
</div>
@endsection
