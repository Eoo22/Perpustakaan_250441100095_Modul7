<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Gate;

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

    // SOAL 2 (b): Rute Pencarian Buku dengan Parameter Binding (Menambal SQLi A2)
    Route::get('/buku-cari', function (Request $request) {
        $judul = $request->input('judul');
        // Parameter binding menggunakan '?' untuk mencegah SQL Injection
        $hasil = DB::select("SELECT * FROM bukus WHERE judul = ?", [$judul]);
        return response()->json($hasil);
    });

    // SOAL 3: Manajemen Buku dilindungi Gate kelola-buku
    Route::middleware(['can:kelola-buku'])->group(function () {
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        // SOAL 2 (c): Penghapusan buku menggunakan method DELETE
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });

    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');

    // SOAL 4: Detail Peminjaman ditutup IDOR-nya menggunakan Policy
    Route::get('/peminjaman/{peminjaman}', function (Peminjaman $peminjaman) {
        Gate::authorize('view', $peminjaman);
        return view('peminjaman.show', compact('peminjaman'));
    })->name('peminjaman.show');
});

require __DIR__.'/auth.php';