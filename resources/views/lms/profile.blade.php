@extends('lms.layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <h4 class="fw-bold mb-4">Profil Saya</h4>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person-circle me-1"></i> Informasi Akun
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                    @endif
                    @if ($errors->hasAny(['name']))
                        <div class="alert alert-danger py-2 small">{{ $errors->first('name') }}</div>
                    @endif

                    <form method="POST" action="{{ route('lms.profil.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $lmsUser->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Email</label>
                            <input type="email" class="form-control" value="{{ $lmsUser->email }}" disabled>
                            <div class="form-text">Email dipakai untuk login, hubungi admin kalau perlu diubah.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Role</label>
                            <input type="text" class="form-control" value="{{ $lmsUser->role_lms_label }}" disabled>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-shield-lock me-1"></i> Ganti Password
                </div>
                <div class="card-body">
                    @if ($errors->hasAny(['current_password', 'password']))
                        <div class="alert alert-danger py-2 small">
                            {{ $errors->first('current_password') ?: $errors->first('password') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lms.profil.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Password Baru</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            <div class="form-text">Minimal 8 karakter.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-key me-1"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0 small">
        <i class="bi bi-info-circle me-1"></i>
        Akun ini dipakai bersama di sistem PKL, SPMB, dan LMS — perubahan nama/password
        di sini otomatis berlaku juga saat login ke modul lain.
    </div>
@endsection
