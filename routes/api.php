<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use App\Models\Kategori;
use Illuminate\Support\Facades\Route;

// Nama route diberi prefix "api." agar tidak bentrok dengan route web (buku.*).
// URL tetap sama: /api/buku, /api/buku/{buku}, dst.
Route::apiResource('buku', BukuController::class)->names('api.buku');

// Daftar kategori untuk pilihan (dropdown) pada form buku di halaman /buku
Route::get('kategori', fn () => response()->json([
    'data' => Kategori::orderBy('nama')->get(['id', 'nama']),
]));

Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);