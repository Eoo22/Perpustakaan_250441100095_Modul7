<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /** Daftar seluruh peminjaman (admin & petugas). */
    public function index(Request $request): View
    {
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    /** Detail peminjaman: wajib lolos otorisasi PeminjamanPolicy@view. */
    public function show($id): View
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku.kategori'])->findOrFail($id);
        Gate::authorize('view', $peminjaman);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /** Form pengajuan peminjaman (semua role). */
    public function create(): View
    {
        return view('peminjaman.create', [
            'bukus' => Buku::orderBy('judul')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'buku_id' => ['required', 'integer', 'exists:bukus,id'],
        ], [
            'buku_id.required' => 'Buku wajib dipilih.',
            'buku_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        $anggota = $request->user()->anggotaAtauBuat();

        $masihAktif = Peminjaman::where('anggota_id', $anggota->id)
            ->where('buku_id', $data['buku_id'])
            ->whereIn('status', [Peminjaman::STATUS_MENUNGGU, Peminjaman::STATUS_DIPINJAM])
            ->exists();

        if ($masihAktif) {
            return back()->withInput()->with('error', 'Anda masih memiliki pengajuan atau pinjaman aktif untuk buku ini.');
        }

        Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $data['buku_id'],
            'tanggal_pinjam' => now()->toDateString(),
            'status' => Peminjaman::STATUS_MENUNGGU,
        ]);

        return redirect()->route('riwayat.index')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function setujui(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_MENUNGGU) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat diproses.');
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIPINJAM,
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_MENUNGGU) {
            return back()->with('error', 'Hanya pengajuan berstatus menunggu yang dapat diproses.');
        }

        $peminjaman->update(['status' => Peminjaman::STATUS_DITOLAK]);

        return back()->with('success', 'Peminjaman ditolak.');
    }
}