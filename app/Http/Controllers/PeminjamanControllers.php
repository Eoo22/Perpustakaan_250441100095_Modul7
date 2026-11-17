<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * SOAL 1 (A4): IDOR pada detail peminjaman tanpa pemeriksaan kepemilikan.
     */
    public function showRentan($id)
    {
        // KERENTANAN A4: Mengambil data menggunakan findOrFail($id) tanpa memeriksa 
        // apakah peminjaman tersebut milik user yang sedang login atau tidak.
        $peminjaman = Peminjaman::findOrFail($id);

        return view('peminjaman.show', compact('peminjaman'));
    }
}