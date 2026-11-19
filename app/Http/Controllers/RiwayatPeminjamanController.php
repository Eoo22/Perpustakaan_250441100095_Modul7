<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RiwayatPeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $peminjamans = Peminjaman::with('buku')
            ->whereHas('anggota', fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(10);

        return view('riwayat.index', compact('peminjamans'));
    }

    public function show(Peminjaman $peminjaman): View
    {
        // 403 jika bukan pemilik (dan bukan admin/petugas)
        Gate::authorize('view', $peminjaman);

        $peminjaman->load(['buku.kategori', 'anggota']);

        return view('riwayat.show', compact('peminjaman'));
    }
}