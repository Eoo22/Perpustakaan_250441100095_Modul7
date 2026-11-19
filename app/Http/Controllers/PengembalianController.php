<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /** Daftar buku yang sedang dipinjam. */
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->orderBy('tanggal_pinjam')
            ->paginate(10);

        return view('pengembalian.index', compact('peminjamans'));
    }

    public function store(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return back()->with('error', 'Hanya buku berstatus dipinjam yang dapat dikembalikan.');
        }

        $peminjaman->update([
            'tanggal_kembali' => now()->toDateString(),
            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
        ]);

        return back()->with('success', 'Pengembalian buku berhasil dicatat.');
    }
}