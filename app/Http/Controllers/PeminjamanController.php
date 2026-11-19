<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isStaff = ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'petugas']))
            || in_array($user?->role ?? null, ['admin', 'petugas'], true);

        $peminjamans = Peminjaman::query()
            ->when(! $isStaff && $user, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        if (view()->exists('riwayat.index')) {
            return view('riwayat.index', compact('peminjamans'));
        }

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function show(Peminjaman $peminjaman): View
    {
        Gate::authorize('view', $peminjaman);

        if (view()->exists('riwayat.show')) {
            return view('riwayat.show', compact('peminjaman'));
        }

        return view('peminjaman.show', compact('peminjaman'));
    }
}