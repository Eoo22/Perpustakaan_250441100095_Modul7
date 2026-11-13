<?php

namespace App\Http\Controllers;

use App\Models\Buku;

class BukuController extends Controller
{
    public function index()
    {
        return view('buku.index');
    }

    public function show(string $id)
    {
        $selectedBuku = Buku::with('kategori')->findOrFail($id);

        return view('buku.show', ['selectedBuku' => $selectedBuku]);
    }
}