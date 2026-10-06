@extends('admin.layouts.app')

@section('title', 'Kelola Guru - ' . $guru->name)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ $guru->name }}</h4>
            <p class="text-muted mb-0">{{ $guru->email }}</p>
        </div>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        {{-- ============ KOLOM KIRI: Penugasan Mengajar ============ --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-1"></i> Tambah Penugasan
                    Mengajar</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.guru.mengajar.store', $guru) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" class="form-select" required>
                                    <option value="">Pilih mata pelajaran</option>
                                    @foreach ($mataPelajaranList as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">Pilih kelas</option>
                                    @foreach ($kelasList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}
                                            ({{ $k->jurusan->nama ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="form-control" value="{{ $tahunAjaran }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Semester</label>
                                <select name="semester" class="form-select" required>
                                    <option value="Ganjil" @selected($semester === 'Ganjil')>Ganjil</option>
                                    <option value="Genap" @selected($semester === 'Genap')>Genap</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-save me-1"></i> Tambahkan
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Daftar Penugasan Mengajar</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Periode</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengampuMapel as $p)
                                <tr>
                                    <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                                    <td>{{ $p->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="small text-muted">{{ $p->tahun_ajaran }} — {{ $p->semester }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.guru.mengajar.destroy', $p) }}"
                                            onsubmit="return confirm('Hapus penugasan ini? Materi/tugas/nilai yang sudah ada di kelas ini ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada penugasan mengajar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============ KOLOM KANAN: Wali Kelas + Kepala Jurusan ============ --}}
        <div class="col-lg-5">

            {{-- Wali Kelas --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-clipboard-data me-1"></i> Wali Kelas</div>
                <div class="card-body">
                    <p class="small text-muted">Periode: <strong>{{ $tahunAjaran }} — {{ $semester }}</strong></p>

                    @if ($waliKelasSaatIni)
                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                            <span>Wali kelas <strong>{{ $waliKelasSaatIni->kelas->nama_kelas ?? '-' }}</strong></span>
                            <form method="POST" action="{{ route('admin.guru.wali-kelas.destroy', $waliKelasSaatIni) }}"
                                onsubmit="return confirm('Cabut status wali kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Cabut</button>
                            </form>
                        </div>
                    @else
                        <p class="text-muted small">Belum jadi wali kelas manapun di periode ini.</p>
                    @endif

                    <form method="POST" action="{{ route('admin.guru.wali-kelas.store', $guru) }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <label class="form-label small">
                            {{ $waliKelasSaatIni ? 'Ganti jadi wali kelas...' : 'Jadikan wali kelas...' }}
                        </label>
                        <select name="kelas_id" class="form-select mb-2" required>
                            <option value="">Pilih kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->jurusan->nama ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Kalau kelas yang dipilih sudah punya wali kelas lain di
                            periode ini, otomatis digantikan guru ini.
                        </p>
                    </form>
                </div>
            </div>

            {{-- Kepala Jurusan --}}
            {{-- Kepala Jurusan --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-diagram-3 me-1"></i> Kepala Jurusan
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.guru.kepala-jurusan.update', $guru) }}">
                        @csrf
                        @method('PUT')

                        <p class="small text-muted">
                            Centang jurusan yang dipimpin guru ini. Satu jurusan hanya boleh punya satu kepala.
                        </p>

                        @foreach ($jurusanList as $j)
                            @php
                                $dipimpinGuruIni = (int) $j->kepala_jurusan_id === (int) $guru->id;
                                $dipimpinLain = $j->kepala_jurusan_id && !$dipimpinGuruIni;
                            @endphp
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="jurusan_ids[]"
                                    value="{{ $j->id }}" id="jurusan-{{ $j->id }}"
                                    @checked($dipimpinGuruIni) @disabled($dipimpinLain)>
                                <label class="form-check-label {{ $dipimpinLain ? 'text-muted' : '' }}"
                                    for="jurusan-{{ $j->id }}">
                                    {{ $j->nama }}
                                    <span class="badge bg-secondary ms-1">{{ $j->kode }}</span>
                                    @if ($dipimpinLain)
                                        <div class="small">
                                            <i class="bi bi-lock-fill"></i>
                                            Sudah dipimpin {{ $j->kepalaJurusan->name ?? '-' }}
                                        </div>
                                    @endif
                                </label>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-dark w-100 mt-2">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Guru otomatis mendapat role PKL kepala jurusan. Untuk
                            mengganti kepala, lepas dulu centang dari guru yang sekarang menjabat.
                        </p>
                    </form>
                </div>
            </div>
            {{-- Guru Kesiswaan --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> Guru Kesiswaan
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.guru.kesiswaan.update', $guru) }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="kesiswaan" value="0">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="kesiswaan"
                                value="1" id="switchKesiswaan" @checked($adalahKesiswaan)>
                            <label class="form-check-label fw-semibold" for="switchKesiswaan">
                                Jadikan guru kesiswaan
                            </label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>

                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Boleh lebih dari satu guru. Guru kesiswaan menyetujui
                            pengajuan PKL semua kelas setelah Guru BK.
                        </p>
                    </form>

                    @if ($daftarKesiswaan->isNotEmpty())
                        <hr>
                        <div class="small text-muted mb-1">Guru kesiswaan saat ini:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($daftarKesiswaan as $k)
                                <span class="badge {{ $k->id === $guru->id ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $k->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>{{-- /col-lg-5 --}}
    </div>{{-- /row --}}
@endsection
