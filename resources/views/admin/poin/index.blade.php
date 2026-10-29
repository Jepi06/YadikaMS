@extends('admin.layouts.app')

@section('title', 'Pengaturan Poin Siswa')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Pengaturan Poin Siswa</h4>
            <p class="text-muted mb-0 small">Atur jenis pelanggaran (poin berkurang) dan prestasi (poin bertambah). Deposit awal tiap siswa 500 poin.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.poin.create', ['tipe' => 'pelanggaran']) }}" class="btn btn-sm btn-danger">
                <i class="bi bi-dash-circle"></i> Tambah Pelanggaran
            </a>
            <a href="{{ route('admin.poin.create', ['tipe' => 'prestasi']) }}" class="btn btn-sm btn-success">
                <i class="bi bi-trophy"></i> Tambah Prestasi
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="alert alert-info border-0 small">
        <i class="bi bi-info-circle"></i>
        Mengubah poin <b>tidak</b> mengubah catatan yang sudah tersimpan — setiap catatan menyimpan nama &amp; poin saat kejadian.
        Jenis yang <b>dinonaktifkan</b> tidak muncul di form guru, tapi riwayatnya tetap ada.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <ul class="nav nav-pills small">
                    <li class="nav-item"><a class="nav-link {{ !$tipe ? 'active' : '' }}" href="{{ route('admin.poin.index') }}">Semua ({{ $jumlah->sum() }})</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tipe === 'pelanggaran' ? 'active' : '' }}" href="{{ route('admin.poin.index', ['tipe' => 'pelanggaran']) }}">Pelanggaran ({{ $jumlah['pelanggaran'] ?? 0 }})</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tipe === 'prestasi' ? 'active' : '' }}" href="{{ route('admin.poin.index', ['tipe' => 'prestasi']) }}">Prestasi ({{ $jumlah['prestasi'] ?? 0 }})</a></li>
                </ul>
                <form method="GET" class="d-flex gap-2">
                    @if ($tipe)<input type="hidden" name="tipe" value="{{ $tipe }}">@endif
                    <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari nama / kategori...">
                    <button class="btn btn-sm btn-dark">Cari</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width:90px">Tipe</th>
                        <th>Kategori</th>
                        <th>Nama</th>
                        <th class="text-center" style="width:90px">Poin</th>
                        <th class="text-center" style="width:90px">Dipakai</th>
                        <th class="text-center" style="width:110px">Status</th>
                        <th class="text-end" style="width:170px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $j)
                        <tr class="{{ $j->aktif ? '' : 'table-secondary text-muted' }}">
                            <td>
                                <span class="badge {{ $j->tipe === 'prestasi' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $j->tipe === 'prestasi' ? 'Prestasi' : 'Pelanggaran' }}
                                </span>
                            </td>
                            <td class="small">{{ $j->kategori }}</td>
                            <td>{{ $j->nama }}</td>
                            <td class="text-center fw-bold font-monospace {{ $j->tipe === 'prestasi' ? 'text-success' : 'text-danger' }}">
                                {{ $j->tipe === 'prestasi' ? '+' : '−' }}{{ $j->poin }}
                            </td>
                            <td class="text-center small">{{ $dipakai[$j->id] ?? 0 }}×</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.poin.toggle', $j) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $j->aktif ? 'btn-outline-success' : 'btn-outline-secondary' }}" type="submit">
                                        {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.poin.edit', $j) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Ubah</a>
                                <form method="POST" action="{{ route('admin.poin.destroy', $j) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus &quot;{{ addslashes($j->nama) }}&quot;?{{ ($dipakai[$j->id] ?? 0) > 0 ? ' Sudah dipakai ' . $dipakai[$j->id] . ' kali; riwayat siswa tetap aman, tapi sebaiknya cukup dinonaktifkan.' : '' }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
