<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status SPMB - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(135deg, #0d2b4e, #12406f);
            color: #fff;
            padding: 3rem 0;
        }
    </style>
</head>

<body class="bg-light">

    <div class="hero text-center">
        <h2 class="fw-bold">Penerimaan Peserta Didik Baru</h2>
        <p class="mb-0">SMK Yadika Soreang &mdash; Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</p>
    </div>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Cek Status Pendaftaran</h5>
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="cari" value="{{ $keyword }}" class="form-control"
                                placeholder="Masukkan No. Pendaftaran atau Nama Lengkap">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>

                        @if ($keyword !== '')
                            <hr>
                            @forelse($hasil as $p)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <div class="fw-semibold">{{ $p->nama_lengkap }}</div>
                                        <div class="small text-muted">{{ $p->no_pendaftaran }} &middot;
                                            {{ optional($p->jurusan)->nama ?? '-' }}</div>
                                    </div>
                                    <div>
                                        @if ($p->status === 'diterima')
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif($p->status === 'ditolak')
                                            <span class="badge bg-danger">Tidak Diterima</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small py-3">Data tidak ditemukan. Periksa kembali nama atau nomor
                                    pendaftaran Anda.</div>
                            @endforelse
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Kuota Penerimaan per Jurusan</h6>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Jurusan</th>
                                    <th class="text-center">Kuota</th>
                                    <th class="text-center">Jumlah Pendaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapKuota as $j)
                                    <tr>
                                        <td>{{ $j->nama }}</td>
                                        <td class="text-center">{{ $j->kuota }}</td>
                                        <td class="text-center">{{ $j->pendaftar_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('spmb.login') }}" class="small text-decoration-none text-muted">Login Admin
                        &rarr;</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
