<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatPeminjamanController;
use App\Http\Controllers\UserController;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Pintu masuk setelah login: diarahkan sesuai role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // ===== Profil (hanya milik sendiri, lihat ProfileController) =====
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat-profil-sendiri')->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ubah-profil-sendiri')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('permission:ubah-profil-sendiri')->name('profile.destroy');

    // ===== Dashboard per role =====
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('permission:akses-dashboard-admin')->name('admin.dashboard');
    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])
        ->middleware('permission:akses-dashboard-petugas')->name('petugas.dashboard');

    // ===== Buku: lihat (semua role) =====
    Route::middleware('permission:lihat-daftar-buku')->group(function () {
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/{buku}', [BukuController::class, 'show'])
            ->whereNumber('buku')->name('buku.show');
    });

    // ===== Buku: kelola (admin, petugas) =====
    Route::middleware('permission:kelola-data-buku')->group(function () {
        Route::get('/buku/tambah', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])
            ->whereNumber('buku')->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])
            ->whereNumber('buku')->name('buku.update');
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])
            ->whereNumber('buku')->name('buku.destroy');
    });

    // ===== Peminjaman =====
    Route::get('/peminjaman/ajukan', [PeminjamanController::class, 'create'])
        ->middleware('permission:ajukan-peminjaman-buku')->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])
        ->middleware('permission:ajukan-peminjaman-buku')->name('peminjaman.store');

    Route::get('/peminjaman', [PeminjamanController::class, 'index'])
        ->middleware('permission:kelola-peminjaman')->name('peminjaman.index');
    Route::post('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])
        ->middleware('permission:proses-persetujuan-peminjaman')->name('peminjaman.setujui');
    Route::post('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])
        ->middleware('permission:proses-persetujuan-peminjaman')->name('peminjaman.tolak');

    // ===== Pengembalian =====
    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->middleware('permission:kelola-pengembalian')->name('pengembalian.index');
    Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])
        ->middleware('permission:catat-pengembalian-buku')->name('pengembalian.store');

    // ===== Riwayat milik sendiri (kepemilikan dicek di Policy) =====
    Route::middleware('permission:lihat-riwayat-peminjaman-sendiri')->group(function () {
        Route::get('/riwayat-peminjaman', [RiwayatPeminjamanController::class, 'index'])
            ->name('riwayat.index');
        Route::get('/riwayat-peminjaman/{peminjaman}', [RiwayatPeminjamanController::class, 'show'])
            ->whereNumber('peminjaman')->name('riwayat.show');
    });

    // ===== CELAH PRAKTIKUM =====

    // A2 (SUDAH DIPERBAIKI): memakai parameter binding
    Route::get('/buku-cari', function (Request $request) {
        $judul = $request->query('judul', '');
        $hasil = DB::select(
            'SELECT * FROM bukus WHERE judul LIKE ?',
            ['%'.$judul.'%']
        );
        return response()->json($hasil);
    });

    // A3 (SUDAH DIPERBAIKI): route GET /buku/{buku}/hapus dihapus.
    // Penghapusan hanya lewat DELETE /buku/{buku} (buku.destroy) di atas.

    // A4 (BELUM DIPERBAIKI, ditutup di Soal 4): IDOR
    Route::get('/peminjaman/{id}', function ($id) {
        return response()->json(Peminjaman::findOrFail($id));
    })->whereNumber('id');

    // ===== Laporan =====
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat-laporan-perpustakaan')->name('laporan.index');

    // ===== Pengguna & role (khusus admin) =====
    Route::middleware('permission:kelola-data-pengguna')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/auth.php';