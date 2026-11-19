<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Soal 4: Route daftar & detail peminjaman / riwayat dengan otorisasi Policy
    if (class_exists(\App\Models\Peminjaman::class) && class_exists(PeminjamanController::class)) {
        Route::get('/riwayat', [PeminjamanController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{peminjaman}', [PeminjamanController::class, 'show'])->name('riwayat.show');
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    } else {
        Route::get('/riwayat', function () {
            return redirect()->route('buku.index');
        })->name('riwayat.index');
    }

    // Soal 2 & 3: Pencarian, daftar buku, dan pengelolaan buku via Gate kelola-buku
    Route::get('/buku-cari', [BukuController::class, 'cari'])->name('buku.cari');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');

    Route::middleware('can:kelola-buku')->group(function () {
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::get('/buku/tambah', [BukuController::class, 'create']);
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::patch('/buku/{buku}', [BukuController::class, 'update']);
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });

    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
});

require __DIR__.'/auth.php';