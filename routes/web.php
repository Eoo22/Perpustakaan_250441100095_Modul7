<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/riwayat', function () {
        $peminjamans = class_exists(\App\Models\Peminjaman::class)
            ? \App\Models\Peminjaman::latest()->get()
            : collect();

        if (view()->exists('riwayat.index')) {
            return view('riwayat.index', compact('peminjamans'));
        }

        if (view()->exists('peminjaman.index')) {
            return view('peminjaman.index', compact('peminjamans'));
        }

        return redirect()->route('buku.index');
    })->name('riwayat.index');

    // Route GET /buku-cari di balik middleware auth
    Route::get('/buku-cari', [BukuController::class, 'cari'])->name('buku.cari');

    // Route baca buku (semua user login)
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');

    // Soal 3: Route tambah, ubah, dan hapus buku dilindungi middleware can:kelola-buku
    Route::middleware('can:kelola-buku')->group(function () {
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::get('/buku/tambah', [BukuController::class, 'create']);
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::patch('/buku/{buku}', [BukuController::class, 'update']);
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });

    // Route detail buku diletakkan setelah route statis (/buku/create, /buku/tambah)
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
});

require __DIR__.'/auth.php';