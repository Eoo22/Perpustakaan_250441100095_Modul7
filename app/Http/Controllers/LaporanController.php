<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        $bukuPerKategori = DB::table('kategoris')
            ->leftJoin('bukus', 'kategoris.id', '=', 'bukus.kategori_id')
            ->select('kategoris.nama as kategori', DB::raw('count(bukus.id) as total_buku'))
            ->groupBy('kategoris.id', 'kategoris.nama')
            ->get();

        $peminjamanPerBuku = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->get();

        $perStatus = Peminjaman::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('laporan.index', compact('bukuPerKategori', 'peminjamanPerBuku', 'perStatus'));
    }
}