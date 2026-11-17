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
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rute Profile bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Buku & Skenario Celah A1 & A2
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    
    // SOAL 1 (A2): Rute pencarian rentan SQL Injection
    Route::get('/buku-cari', [BukuController::class, 'cariRentan'])->name('buku.cari');

    // SOAL 1 (A3): Rute penghapusan buku menggunakan HTTP GET
    Route::get('/buku/{buku}/hapus', [BukuController::class, 'destroyGetRentan'])->name('buku.hapus.get');

    // CRUD Buku standar
    Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
    Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');

    // SOAL 1 (A4): Rute detail peminjaman rentan IDOR
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'showRentan'])->name('peminjaman.show');
});

require __DIR__.'/auth.php';