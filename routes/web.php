<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // pindah ke atas

/*
|--------------------------------------------------------------------------
| PKL – Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Mapping\{
    DashboardController,
    SiswaController,
    GuruPembimbingController,
    TempatPklController,
    PenempatanPklController,
    ApprovalController,
    AuthController,
    DashboardPublicController as PklDashboardPublicController,
    SIswaPklController,
    PengajuanPklPublicController,
    ProfileController as PklProfileController
};

/*
|--------------------------------------------------------------------------
| SPMB – Controllers
| (sebelumnya bernama PPDB; namespace & guard sudah diganti ke "spmb")
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Spmb\Auth\AuthController as SpmbAuthController;
use App\Http\Controllers\Spmb\Admin\PendaftarController as SpmbPendaftarController;
use App\Http\Controllers\Spmb\DashboardPublicController as SpmbDashboardPublicController;
use App\Http\Controllers\Spmb\PengajuanSpmbPublicController; // ← tambahan baru
use App\Http\Controllers\Spmb\ProfileController as SpmbProfileController;
//LMS

use App\Http\Controllers\Lms\Auth\AuthController as LmsAuthController;
use App\Http\Controllers\Lms\DashboardPublicController as LmsDashboardPublicController;
use App\Http\Controllers\Lms\FileController as LmsFileController;
use App\Http\Controllers\Lms\ProfileController as LmsProfileController;
use App\Http\Controllers\Lms\Admin\DashboardController as LmsAdminDashboardController;
use App\Http\Controllers\Lms\Guru\DashboardController as LmsGuruDashboardController;
use App\Http\Controllers\Lms\Guru\KelasController as LmsGuruKelasController;
use App\Http\Controllers\Lms\Guru\PresensiController as LmsGuruPresensiController;
use App\Http\Controllers\Lms\Guru\MateriController as LmsGuruMateriController;
use App\Http\Controllers\Lms\Guru\TugasController as LmsGuruTugasController;
use App\Http\Controllers\Lms\Siswa\DashboardController as LmsSiswaDashboardController;
use App\Http\Controllers\Lms\Siswa\KelasController as LmsSiswaKelasController;
use App\Http\Controllers\Lms\Siswa\PresensiController as LmsSiswaPresensiController;
use App\Http\Controllers\Lms\Siswa\MateriController as LmsSiswaMateriController;
use App\Http\Controllers\Lms\Siswa\TugasController as LmsSiswaTugasController;

// super admin

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PenggunaController as SuperAdminPenggunaController;
/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('Dashboard');
})->name('home');

/*
|==========================================================================
| PKL – PUBLIC
|==========================================================================
*/
Route::prefix('pkl')->group(function () {

    Route::get('/', [PklDashboardPublicController::class, 'index'])
        ->name('pkl');

    Route::get('/pengajuan', [PengajuanPklPublicController::class, 'create'])
        ->name('pkl.pengajuan.create');
    Route::post('/pengajuan', [PengajuanPklPublicController::class, 'store'])
        ->name('pkl.pengajuan.store');

    Route::get('/pengajuan/api/kelas/{kelas}/siswa', [SiswaController::class, 'byKelas'])
        ->name('pkl.pengajuan.api.kelas.siswa');

    Route::middleware('guest:pkl')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('pkl.login');
        Route::post('/login', [AuthController::class, 'login'])->name('pkl.login.process');
    });
});

Route::post('/pkl/logout', [AuthController::class, 'logout'])
    ->middleware('auth.pkl')
    ->name('pkl.logout');

/*
|==========================================================================
| PKL – PROTECTED
|==========================================================================
*/
Route::prefix('pkl')->middleware('auth.pkl')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pkl.dashboard');

    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    Route::get('/guru', [GuruPembimbingController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruPembimbingController::class, 'store'])->name('guru.store');
    Route::put('/guru/{guru}', [GuruPembimbingController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [GuruPembimbingController::class, 'destroy'])->name('guru.destroy');

    Route::get('/tempat-pkl', [TempatPklController::class, 'index'])->name('tempat.index');
    Route::post('/tempat-pkl', [TempatPklController::class, 'store'])->name('tempat.store');
    Route::put('/tempat-pkl/{tempat}', [TempatPklController::class, 'update'])->name('tempat.update');
    Route::delete('/tempat-pkl/{tempat}', [TempatPklController::class, 'destroy'])->name('tempat.destroy');

    Route::get('/penempatan', [PenempatanPklController::class, 'index'])->name('penempatan.index');
    Route::post('/penempatan', [PenempatanPklController::class, 'store'])->name('penempatan.store');
    Route::get('/penempatan/{penempatan}', [PenempatanPklController::class, 'show'])->name('penempatan.show');
    Route::put('/penempatan/{penempatan}', [PenempatanPklController::class, 'update'])->name('penempatan.update');
    Route::delete('/penempatan/{penempatan}', [PenempatanPklController::class, 'destroy'])->name('penempatan.destroy');
    Route::post('/penempatan/{penempatan}/ajukan', [PenempatanPklController::class, 'ajukan'])->name('penempatan.ajukan');

    Route::patch('/penempatan/{penempatan}/lengkapi', [PenempatanPklController::class, 'lengkapi'])
        ->name('penempatan.lengkapi');

    Route::middleware('role:wali_kelas,guru_bk,kesiswaan,kepala_jurusan')->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approval/{penempatan}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('/approval/{penempatan}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    });

    Route::prefix('api')->group(function () {
        Route::get('/kelas/{kelas}/siswa', [SiswaController::class, 'byKelas'])->name('api.kelas.siswa');
    });

    Route::get('/penempatan/{penempatan}/surat-rekomendasi', [PenempatanPklController::class, 'cetakSuratRekomendasi'])
        ->name('penempatan.surat-rekomendasi');

    Route::get('/siswa/belum-mengajukan', [SiswaController::class, 'belumMengajukan'])
        ->name('siswa.belum-mengajukan');

    Route::middleware('role:siswa')->prefix('siswa-pkl')->name('siswa.pkl.')->group(function () {
        Route::get('/status', [SIswaPklController::class, 'status'])->name('status');
        Route::get('/ajukan', [SIswaPklController::class, 'create'])->name('create');
        Route::post('/ajukan', [SIswaPklController::class, 'store'])->name('store');
    });
    Route::get('/profil', [PklProfileController::class, 'edit'])->name('pkl.profil.edit');
    Route::put('/profil', [PklProfileController::class, 'update'])->name('pkl.profil.update');
    Route::put('/profil/password', [PklProfileController::class, 'updatePassword'])->name('pkl.profil.password');
});

/*
|==========================================================================
| SPMB – PUBLIC (landing page, tanpa login)
| Hanya menampilkan nama, jurusan, dan status diterima/tidak.
|==========================================================================
*/
Route::prefix('spmb')->name('spmb.')->group(function () {

    Route::get('/', [SpmbDashboardPublicController::class, 'index'])->name('public.index');

    // Pengajuan mandiri oleh siswa/calon siswa — tanpa login
    Route::get('/pengajuan', [PengajuanSpmbPublicController::class, 'create'])
        ->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanSpmbPublicController::class, 'store'])
        ->name('pengajuan.store');
    Route::get('/pengajuan/berhasil/{noPendaftaran}', [PengajuanSpmbPublicController::class, 'berhasil'])
        ->name('pengajuan.berhasil');

    // guest:spmb → guard 'spmb' di config/auth.php
    Route::middleware('guest:spmb')->group(function () {
        Route::get('/login', [SpmbAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [SpmbAuthController::class, 'login'])->name('login.process');
    });
});

// Logout SPMB – di luar prefix supaya middleware auth.spmb berdiri sendiri
Route::post('/spmb/logout', [SpmbAuthController::class, 'logout'])
    ->middleware('auth.spmb')
    ->name('spmb.logout');

/*
|==========================================================================
| SPMB – PROTECTED (hanya admin SPMB)
|==========================================================================
*/
Route::prefix('spmb/admin')->name('spmb.admin.')->middleware('auth.spmb')->group(function () {

    Route::get('/dashboard', [SpmbPendaftarController::class, 'dashboard'])->name('dashboard');

    // Semua pendaftar (semua jurusan)
    Route::get('/pendaftar', [SpmbPendaftarController::class, 'index'])->name('pendaftar.index');

    // Sidebar per jurusan
    Route::get('/pendaftar/jurusan/{jurusan}', [SpmbPendaftarController::class, 'perJurusan'])
        ->name('pendaftar.per-jurusan');

    Route::get('/pendaftar/create', [SpmbPendaftarController::class, 'create'])->name('pendaftar.create');
    Route::post('/pendaftar', [SpmbPendaftarController::class, 'store'])->name('pendaftar.store');
    Route::get('/pendaftar/{pendaftar}', [SpmbPendaftarController::class, 'show'])->name('pendaftar.show');
    Route::get('/pendaftar/{pendaftar}/edit', [SpmbPendaftarController::class, 'edit'])->name('pendaftar.edit');
    Route::put('/pendaftar/{pendaftar}', [SpmbPendaftarController::class, 'update'])->name('pendaftar.update');
    Route::delete('/pendaftar/{pendaftar}', [SpmbPendaftarController::class, 'destroy'])->name('pendaftar.destroy');

    // Popup tambah/edit nominal pembayaran
    Route::patch('/pendaftar/{pendaftar}/nominal', [SpmbPendaftarController::class, 'updateNominal'])
        ->name('pendaftar.nominal');

    // Tombol Lunas (ceklis hijau) & pembatalannya
    Route::patch('/pendaftar/{pendaftar}/lunas', [SpmbPendaftarController::class, 'markLunas'])
        ->name('pendaftar.lunas');
    Route::patch('/pendaftar/{pendaftar}/batal-lunas', [SpmbPendaftarController::class, 'unmarkLunas'])
        ->name('pendaftar.batal-lunas');

    // Override status manual (mis. tolak pendaftar)
    Route::patch('/pendaftar/{pendaftar}/status', [SpmbPendaftarController::class, 'updateStatus'])
        ->name('pendaftar.status');

    // Export
    Route::get('/export/pdf', [SpmbPendaftarController::class, 'exportPdf'])->name('export.pdf');
    // Export
    Route::get('/export/excel', [SpmbPendaftarController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/excel/jurusan/{jurusan}', [SpmbPendaftarController::class, 'exportExcelPerJurusan'])
        ->name('export.excel.per-jurusan'); // ← tambahan baru
    Route::get('/export/pdf', [SpmbPendaftarController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/profil', [SpmbProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [SpmbProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [SpmbProfileController::class, 'updatePassword'])->name('profil.password');
});


/*
|==========================================================================
| LMS – PUBLIC (landing page + login)
|==========================================================================
*/
Route::prefix('lms')->group(function () {

    Route::get('/', [LmsDashboardPublicController::class, 'index'])->name('lms');

    Route::middleware('guest:lms')->group(function () {
        Route::get('/login', [LmsAuthController::class, 'showLogin'])->name('lms.login');
        Route::post('/login', [LmsAuthController::class, 'login'])->name('lms.login.process');
    });
});

Route::post('/lms/logout', [LmsAuthController::class, 'logout'])
    ->middleware('auth.lms')
    ->name('lms.logout');

/*
|==========================================================================
| LMS – FILE (materi / tugas / jawaban) — serve lewat Laravel, bukan
| symlink public/storage, supaya gak kena blokir web server + ada
| pengecekan otorisasi per file.
|==========================================================================
*/
Route::prefix('lms/file')->name('lms.file.')
    ->middleware('auth.lms')
    ->group(function () {
        Route::get('/materi/{materi}', [LmsFileController::class, 'materi'])->name('materi');
        Route::get('/tugas/{tugas}', [LmsFileController::class, 'tugasLampiran'])->name('tugas');
        Route::get('/jawaban/{pengumpulan}', [LmsFileController::class, 'jawaban'])->name('jawaban');
    });

/*
|==========================================================================
| LMS – PROFIL (semua role: admin/guru/siswa ubah data diri sendiri)
|==========================================================================
*/
Route::prefix('lms/profil')->name('lms.profil.')
    ->middleware('auth.lms')
    ->group(function () {
        Route::get('/', [LmsProfileController::class, 'edit'])->name('edit');
        Route::put('/', [LmsProfileController::class, 'update'])->name('update');
        Route::put('/password', [LmsProfileController::class, 'updatePassword'])->name('password');
    });

/*
|==========================================================================
| LMS – ADMIN
|==========================================================================
*/
Route::prefix('lms/admin')->name('lms.admin.')
    ->middleware(['auth.lms', 'role.lms:admin'])
    ->group(function () {
        Route::get('/dashboard', [LmsAdminDashboardController::class, 'index'])->name('dashboard');
    });

/*
|==========================================================================
| LMS – GURU
|==========================================================================
*/
Route::prefix('lms/guru')->name('lms.guru.')
    ->middleware(['auth.lms', 'role.lms:guru'])
    ->group(function () {
        Route::get('/dashboard', [LmsGuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kelas', [LmsGuruKelasController::class, 'index'])->name('kelas.index');

        Route::get('/kelas/{pengampuMapel}/presensi', [LmsGuruPresensiController::class, 'index'])
            ->name('presensi.index');
        Route::post('/kelas/{pengampuMapel}/presensi/buka', [LmsGuruPresensiController::class, 'buka'])
            ->name('presensi.buka');
        Route::post('/kelas/{pengampuMapel}/presensi/tutup', [LmsGuruPresensiController::class, 'tutup'])
            ->name('presensi.tutup');
        Route::post('/kelas/{pengampuMapel}/presensi/manual', [LmsGuruPresensiController::class, 'simpanManual'])
            ->name('presensi.manual');
        Route::get('/kelas/{pengampuMapel}/presensi/rekap', [LmsGuruPresensiController::class, 'rekap'])
            ->name('presensi.rekap');

        Route::get('/kelas/{pengampuMapel}/materi', [LmsGuruMateriController::class, 'index'])
            ->name('materi.index');
        Route::post('/kelas/{pengampuMapel}/materi', [LmsGuruMateriController::class, 'store'])
            ->name('materi.store');
        Route::delete('/materi/{materi}', [LmsGuruMateriController::class, 'destroy'])
            ->name('materi.destroy');

        Route::get('/kelas/{pengampuMapel}/tugas', [LmsGuruTugasController::class, 'index'])
            ->name('tugas.index');
        Route::post('/kelas/{pengampuMapel}/tugas', [LmsGuruTugasController::class, 'store'])
            ->name('tugas.store');
        Route::delete('/tugas/{tugas}', [LmsGuruTugasController::class, 'destroy'])
            ->name('tugas.destroy');
        Route::get('/tugas/{tugas}/kumpulan', [LmsGuruTugasController::class, 'kumpulan'])
            ->name('tugas.kumpulan');
        Route::post('/pengumpulan/{pengumpulan}/nilai', [LmsGuruTugasController::class, 'simpanNilai'])
            ->name('tugas.kumpulan.nilai');
    });

/*
|==========================================================================
| LMS – SISWA
|==========================================================================
*/
Route::prefix('lms/siswa')->name('lms.siswa.')
    ->middleware(['auth.lms', 'role.lms:siswa'])
    ->group(function () {
        Route::get('/dashboard', [LmsSiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kelas', [LmsSiswaKelasController::class, 'index'])->name('kelas.index');

        Route::get('/presensi/scan/{token}', [LmsSiswaPresensiController::class, 'scan'])
            ->name('presensi.scan');
        Route::get('/presensi/kamera', [LmsSiswaPresensiController::class, 'kamera'])
            ->name('presensi.kamera');
        Route::get('/presensi', [LmsSiswaPresensiController::class, 'riwayat'])
            ->name('presensi.riwayat');

        Route::get('/kelas/{pengampuMapel}/materi', [LmsSiswaMateriController::class, 'index'])
            ->name('materi.index');

        Route::get('/kelas/{pengampuMapel}/tugas', [LmsSiswaTugasController::class, 'index'])
            ->name('tugas.index');
        Route::get('/tugas/{tugas}', [LmsSiswaTugasController::class, 'show'])
            ->name('tugas.show');
        Route::post('/tugas/{tugas}/kumpul', [LmsSiswaTugasController::class, 'kumpul'])
            ->name('tugas.kumpul');
    });


/*
|==========================================================================
| PANEL SUPER ADMIN — guard-agnostic (bisa dibuka dari login PKL, SPMB,
| atau LMS manapun, selama akunnya is_super_admin = true). Lihat
| middleware EnsureSuperAdmin (alias 'super.admin').
|==========================================================================
*/
Route::prefix('admin')->name('admin.')->middleware('super.admin')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/pengguna', [SuperAdminPenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/{user}/akses', [SuperAdminPenggunaController::class, 'akses'])->name('pengguna.akses');
    Route::put('/pengguna/{user}/akses', [SuperAdminPenggunaController::class, 'updateAkses'])->name('pengguna.akses.update');
});
