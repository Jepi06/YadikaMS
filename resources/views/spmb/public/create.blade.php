<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran SPMB - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-brand {
            font-weight: 700;
        }
    </style>
</head>

<body>

    {{-- ============ NAVBAR ============ --}}
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg,#0d2b4e,#12406f);">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SMK Yadika Soreang</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navMain">
                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#daftar-pendaftar">Status Pendaftar</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-lg-2" href="{{ route('spmb.login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="text-white text-center py-4" style="background: linear-gradient(135deg,#0d2b4e,#12406f);">
        <h2 class="fw-bold mb-1">Formulir Pendaftaran</h2>
        <p class="mb-0">Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</p>
    </div>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali isian Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Data Calon Peserta Didik</h5>
                        <p class="text-muted small">Lengkapi data di bawah ini dengan benar. Nomor pendaftaran akan
                            dibuat otomatis setelah Anda mengirim formulir.</p>

                        <form method="POST" action="{{ route('spmb.daftar.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    class="form-control" required maxlength="150">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                    class="form-control" maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2" maxlength="255">{{ old('alamat') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
                                    class="form-control" maxlength="150">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP / WhatsApp <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                        class="form-control" required maxlength="20">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control" maxlength="150">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jurusan Pilihan <span class="text-danger">*</span></label>
                                <select name="jurusan_id" class="form-select" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($jurusanList as $jur)
                                        <option value="{{ $jur->id }}"
                                            {{ (string) old('jurusan_id') === (string) $jur->id ? 'selected' : '' }}>
                                            {{ $jur->nama }} (Kuota: {{ $jur->kuota }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Catatan: sengaja TIDAK ada input nominal/biaya di form ini.
                                 Nilai tersebut hanya diisi/diubah oleh admin lewat panel admin. --}}

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-check"></i> Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
