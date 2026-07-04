<?php

use Illuminate\Support\Facades\Route;

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
    DashboardPublicController,
    SIswaPklController,
    PengajuanPklPublicController
};

/*
|--------------------------------------------------------------------------
| PPDB – Controllers
| Namespace: App\Http\Controllers\Ppdb\Admin  (konsisten dengan file asli)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Ppdb\Auth\AuthController as PpdbAuthController;
use App\Http\Controllers\Ppdb\Admin\PendaftarController as PpdbPendaftarController; // typo 'Controlle' diperbaiki


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

    Route::get('/', [DashboardPublicController::class, 'index'])
        ->name('pkl');

    /*
     * BARU: Pengajuan tempat PKL secara PUBLIK (tanpa login) oleh siswa.
     *
     * PERBAIKAN: sebelumnya route '/pengajuan' di sini diberi nama
     * 'siswa.pkl.create' -> BENTROK dengan nama route yang sama persis
     * di grup self-service siswa (middleware auth.pkl) di bawah, yang
     * juga menghasilkan nama 'siswa.pkl.create' lewat ->name('siswa.pkl.')
     * + name('create'). Dua route beda tujuan (satu publik tanpa login,
     * satu untuk siswa yang sudah login) tidak boleh berbagi nama yang
     * sama karena route() helper jadi ambigu. Sekarang diberi nama unik
     * 'pkl.pengajuan.*' dan diarahkan ke PengajuanPklPublicController
     * (controller lama SiswaPklController::Pengajuan() juga tidak pernah
     * benar-benar ada / tidak terpakai).
     */
    Route::get('/pengajuan', [PengajuanPklPublicController::class, 'create'])
        ->name('pkl.pengajuan.create');
    Route::post('/pengajuan', [PengajuanPklPublicController::class, 'store'])
        ->name('pkl.pengajuan.store');

    // Endpoint AJAX publik: ambil daftar siswa per kelas TANPA login
    // (dipakai form pengajuan publik untuk memuat nama siswa per kelas).
    Route::get('/pengajuan/api/kelas/{kelas}/siswa', [SiswaController::class, 'byKelas'])
        ->name('pkl.pengajuan.api.kelas.siswa');

    // guest:pkl → guard 'pkl' di config/auth.php
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

    // SISWA
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    // GURU PEMBIMBING
    Route::get('/guru', [GuruPembimbingController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruPembimbingController::class, 'store'])->name('guru.store');
    Route::put('/guru/{guru}', [GuruPembimbingController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [GuruPembimbingController::class, 'destroy'])->name('guru.destroy');

    // TEMPAT PKL
    Route::get('/tempat-pkl', [TempatPklController::class, 'index'])->name('tempat.index');
    Route::post('/tempat-pkl', [TempatPklController::class, 'store'])->name('tempat.store');
    Route::put('/tempat-pkl/{tempat}', [TempatPklController::class, 'update'])->name('tempat.update');
    Route::delete('/tempat-pkl/{tempat}', [TempatPklController::class, 'destroy'])->name('tempat.destroy');

    // PENEMPATAN PKL
    Route::get('/penempatan', [PenempatanPklController::class, 'index'])->name('penempatan.index');
    Route::post('/penempatan', [PenempatanPklController::class, 'store'])->name('penempatan.store');
    Route::get('/penempatan/{penempatan}', [PenempatanPklController::class, 'show'])->name('penempatan.show');
    Route::put('/penempatan/{penempatan}', [PenempatanPklController::class, 'update'])->name('penempatan.update');
    Route::delete('/penempatan/{penempatan}', [PenempatanPklController::class, 'destroy'])->name('penempatan.destroy');
    Route::post('/penempatan/{penempatan}/ajukan', [PenempatanPklController::class, 'ajukan'])->name('penempatan.ajukan');

    // BARU: Admin melengkapi guru pembimbing & jadwal untuk pengajuan
    // publik siswa (lihat PenempatanPklController::lengkapi()).
    Route::patch('/penempatan/{penempatan}/lengkapi', [PenempatanPklController::class, 'lengkapi'])
        ->name('penempatan.lengkapi');

    // APPROVAL – hanya role tertentu
    Route::middleware('role:wali_kelas,guru_bk,kesiswaan,kepala_jurusan')->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approval/{penempatan}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('/approval/{penempatan}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    });

    // API AJAX
    Route::prefix('api')->group(function () {
        Route::get('/kelas/{kelas}/siswa', [SiswaController::class, 'byKelas'])->name('api.kelas.siswa');
    });
    // Cetak surat rekomendasi PKL (PDF) — admin & kepala jurusan
    Route::get('/penempatan/{penempatan}/surat-rekomendasi', [PenempatanPklController::class, 'cetakSuratRekomendasi'])
        ->name('penempatan.surat-rekomendasi');

    // Admin: daftar siswa yang belum mengajukan PKL
    Route::get('/siswa/belum-mengajukan', [SiswaController::class, 'belumMengajukan'])
        ->name('siswa.belum-mengajukan');

    // Self-service SISWA — dibatasi role_pkl = siswa
    Route::middleware('role:siswa')->prefix('siswa-pkl')->name('siswa.pkl.')->group(function () {
        Route::get('/status', [SiswaPklController::class, 'status'])->name('status');
        Route::get('/ajukan', [SiswaPklController::class, 'create'])->name('create');
        Route::post('/ajukan', [SiswaPklController::class, 'store'])->name('store');
    });
});

/*
|==========================================================================
| PPDB – PUBLIC (tanpa login)
|==========================================================================
*/
// Route::prefix('ppdb')->name('ppdb.')->group(function () {

//     // Halaman cek status untuk peserta didik (tidak perlu login)
//     Route::get('/cek-status', [PpdbPendaftarController::class, 'cekStatus'])->name('cek-status');

//     // guest:ppdb → guard 'ppdb' di config/auth.php
//     Route::middleware('guest:ppdb')->group(function () {
//         Route::get('/login', [PpdbAuthController::class, 'showLogin'])->name('login');
//         Route::post('/login', [PpdbAuthController::class, 'login'])->name('login.process');
//     });
// });

// // Logout PPDB – di luar prefix supaya middleware auth.ppdb berdiri sendiri
// Route::post('/ppdb/logout', [PpdbAuthController::class, 'logout'])
//     ->middleware('auth.ppdb')
//     ->name('ppdb.logout');

// /*
// |==========================================================================
// | PPDB – PROTECTED (admin PPDB)
// |==========================================================================
// */
// Route::prefix('ppdb')->name('ppdb.')->middleware('auth.ppdb')->group(function () {

//     Route::get('/dashboard', [PpdbPendaftarController::class, 'dashboard'])->name('dashboard');

//     // Manajemen Pendaftar
//     Route::get('/pendaftar', [PpdbPendaftarController::class, 'index'])->name('pendaftar.index');
//     Route::get('/pendaftar/{pendaftar}', [PpdbPendaftarController::class, 'show'])->name('pendaftar.show');
//     Route::patch('/pendaftar/{pendaftar}/status', [PpdbPendaftarController::class, 'updateStatus'])->name('pendaftar.updateStatus');

//     // Export
//     Route::get('/export/excel', [PpdbPendaftarController::class, 'exportExcel'])->name('export.excel');
//     Route::get('/export/pdf', [PpdbPendaftarController::class, 'exportPdf'])->name('export.pdf');
// });

    