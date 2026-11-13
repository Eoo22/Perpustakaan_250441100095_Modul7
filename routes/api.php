<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use App\Models\Kategori;
use Illuminate\Support\Facades\Route;

Route::apiResource('buku', BukuController::class);

// Daftar kategori untuk pilihan (dropdown) pada form buku di halaman /buku
Route::get('kategori', fn () => response()->json([
    'data' => Kategori::orderBy('nama')->get(['id', 'nama']),
]));

Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
