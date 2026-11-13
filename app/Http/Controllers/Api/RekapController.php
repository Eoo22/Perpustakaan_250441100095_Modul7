<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    // Endpoint 1: Jumlah buku per kategori
    public function bukuPerKategori()
    {
        $rekap = DB::table('kategoris')
            ->leftJoin('bukus', 'kategoris.id', '=', 'bukus.kategori_id')
            ->select('kategoris.nama as kategori', DB::raw('count(bukus.id) as total_buku'))
            ->groupBy('kategoris.id', 'kategoris.nama')
            ->get();

        return response()->json([
            'success' => true, 
            'data' => $rekap
        ], 200);
    }

    // Endpoint 2: Total peminjaman per buku
    public function peminjamanPerBuku()
    {
        $rekap = Buku::withCount('peminjaman')->get(['id', 'judul', 'peminjaman_count']);

        return response()->json([
            'success' => true, 
            'data' => $rekap
        ], 200);
    }
}