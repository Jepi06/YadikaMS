<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran - SMK Yadika Soreang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- CSS terpisah: public/css/spmb-form.css --}}
   <link href="/css/SPMBForm.css" rel="stylesheet">
</head>

<body>

    {{-- Background decor --}}
    <div class="ambient-glow">
        <div class="glow-circle glow-circle-1"></div>
        <div class="glow-circle glow-circle-2"></div>
        <div class="glow-circle glow-circle-3"></div>
    </div>
    <div class="bg-pattern"></div>

    {{-- ========== NAVBAR ========== --}}
    <nav class="modern-navbar py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="text-white text-decoration-none d-flex align-items-center gap-2 fw-bold"
                href="{{ route('spmb.public.index') }}">
                <span class="brand-badge-icon"><i class="bi bi-mortarboard-fill"></i></span>
                <div>
                    <span class="fs-6 d-block lh-1">SPMB SMK Yadika Soreang</span>
                    <small class="text-info fw-normal" style="font-size: .75rem;">Portal Penerimaan Siswa Baru</small>
                </div>
            </a>

            <a href="{{ route('spmb.public.index') }}"
                class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-search"></i>
                <span>Cek Status</span>
            </a>
        </div>
    </nav>

    {{-- ========== CONTENT ========== --}}
    <main class="container py-4 py-md-5 position-relative" style="z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                {{-- Header --}}
                <div class="text-center mb-4">
                    <div class="badge-pill-spmb mb-2">
                        <i class="bi bi-stars text-primary"></i>
                        <span>Penerimaan Peserta Didik Baru</span>
                    </div>
                    <h2 class="fw-bold hero-title text-gradient-ocean mb-2">
                        Formulir Pendaftaran Siswa Baru
                    </h2>
                    <p class="text-muted mx-auto" style="max-width: 580px; font-size: .95rem;">
                        Isi data dengan lengkap dan benar sesuai berkas resmi. Setelah submit, Anda akan
                        mendapatkan nomor pendaftaran untuk memantau status seleksi.
                    </p>
                </div>

                {{-- Petunjuk --}}
                <div class="info-hint-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>Petunjuk Pengisian:</strong> Pastikan Nomor WhatsApp aktif dan nama lengkap
                        sesuai dengan Kartu Keluarga (KK) atau Ijazah SMP/MTs.
                    </div>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm mb-4 border-0">
                        <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                        </div>
                        <ul class="mb-0 small ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <div class="card glass-card p-4 p-md-5 mb-4">
                    <form method="POST" action="{{ route('spmb.pengajuan.store') }}">
                        @csrf

                        {{-- Bagian 1: Data Calon Siswa --}}
                        <div class="form-section-title">
                            <span class="form-section-icon"><i class="bi bi-person-fill"></i></span>
                            <span>Data Calon Siswa</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nama_lengkap">
                                    <span>Nama Lengkap</span><span class="req">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <i class="bi bi-person input-icon"></i>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                                        value="{{ old('nama_lengkap') }}"
                                        placeholder="Contoh: Muhammad Rizky Pratama" required>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label" for="jenis_kelamin">
                                    <span>Jenis Kelamin</span><span class="req">*</span>
                                </label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label" for="agama">
                                    <span>Agama</span><span class="req">*</span>
                                </label>
                                <select id="agama" name="agama" class="form-select" required>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama') === $agama)>
                                            {{ $agama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="jurusan_id">
                                    <span>Jurusan Pilihan</span><span class="req">*</span>
                                </label>
                                <select id="jurusan_id" name="jurusan_id" class="form-select" required>
                                    <option value="">-- Pilih Jurusan Keahlian --</option>
                                    @foreach ($jurusanList as $j)
                                        <option value="{{ $j->id }}" @selected((int) old('jurusan_id') === $j->id)>
                                            {{ $j->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="asal_sekolah">
                                    <span>Asal Sekolah (SMP / MTs)</span><span class="req">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <i class="bi bi-building input-icon"></i>
                                    <input type="text" id="asal_sekolah" name="asal_sekolah" class="form-control"
                                        value="{{ old('asal_sekolah') }}"
                                        placeholder="Contoh: SMP Negeri 1 Soreang" required>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian 2: Kontak & Orang Tua --}}
                        <div class="form-section-title">
                            <span class="form-section-icon"><i class="bi bi-people-fill"></i></span>
                            <span>Kontak &amp; Data Orang Tua / Wali</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nama_orang_tua">
                                    <span>Nama Orang Tua / Wali</span><span class="req">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <i class="bi bi-person-heart input-icon"></i>
                                    <input type="text" id="nama_orang_tua" name="nama_orang_tua" class="form-control"
                                        value="{{ old('nama_orang_tua') }}" placeholder="Nama Ayah/Ibu/Wali" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="no_hp">
                                    <span>No. HP (WhatsApp Aktif)</span><span class="req">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <i class="bi bi-whatsapp input-icon text-success"></i>
                                    <input type="tel" id="no_hp" name="no_hp" class="form-control"
                                        value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
                                </div>
                                <small class="text-muted" style="font-size: .78rem;">
                                    Digunakan untuk notifikasi &amp; verifikasi.
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="alamat">
                                    <span>Alamat Lengkap Tempat Tinggal</span><span class="req">*</span>
                                </label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="3"
                                    placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten..." required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        {{-- Persetujuan --}}
                        <div class="form-check mb-4 p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="checkPersetujuan" required>
                            <label class="form-check-label text-muted small" for="checkPersetujuan">
                                Saya menyatakan bahwa seluruh data yang diisikan adalah benar dan dapat
                                dipertanggungjawabkan sesuai dokumen yang berlaku.
                            </label>
                        </div>

                        <button type="submit"
                            class="btn btn-submit-spmb w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-send-fill"></i>
                            <span>Kirim Formulir Pendaftaran</span>
                        </button>
                    </form>
                </div>

                {{-- Kembali --}}
                <div class="text-center">
                    <a href="{{ route('spmb.public.index') }}" class="back-link">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Halaman Cek Status</span>
                    </a>
                </div>

            </div>
        </div>
    </main>

    {{-- ========== FOOTER ========== --}}
    <footer class="spmb-footer text-center">
        <div class="container">
            <p class="mb-1 fw-semibold text-dark">SMK Yadika Soreang &bull; Sistem Penerimaan Siswa Baru</p>
            <p class="mb-0 text-muted small">Mencetak Generasi Berkarakter, Kompeten, dan Siap Kerja</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>