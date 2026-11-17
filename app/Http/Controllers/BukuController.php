<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * Menampilkan halaman utama daftar buku.
     */
    public function index()
    {
        $bukus = Buku::all();
        return view('buku.index', compact('bukus'));
    }

    /**
     * SOAL 1 (A2): SQL Injection melalui DB::select() dengan penggabungan input langsung.
     */
    public function cariRentan(Request $request)
    {
        $judul = $request->input('judul', '');
        
        // KERENTANAN A2: Menggabungkan variabel $judul langsung ke dalam string SQL mentah
        $hasil = DB::select("SELECT * FROM bukus WHERE judul = '$judul'");

        return response()->json($hasil);
    }

    /**
     * SOAL 1 (A3): Penghapusan data menggunakan HTTP GET.
     */
    public function destroyGetRentan($id)
    {
        // KERENTANAN A3: Menerima aksi hapus melalui method GET tanpa validasi CSRF/DELETE
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus via GET.');
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|integer',
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer',
        ]);

        $buku = Buku::create($validated);

        return response()->json(['message' => 'Buku berhasil ditambahkan!', 'data' => $buku], 201);
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|integer',
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer',
        ]);

        $buku->update($validated);

        return response()->json(['message' => 'Buku berhasil diubah!', 'data' => $buku]);
    }

    /**
     * Menghapus buku standar (bawaan modul sebelumnya).
     */
    public function destroy(Buku $buku)
    {
        $buku->delete();
        return response()->json(['message' => 'Buku berhasil dihapus!']);
    }
}