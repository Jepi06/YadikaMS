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
    PengajuanPklPublicController
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
});
