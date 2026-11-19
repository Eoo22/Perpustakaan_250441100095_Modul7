<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->can('akses-dashboard-admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->can('akses-dashboard-petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        return view('dashboard');
    }

    public function admin(): View
    {
        return view('dashboard-admin', [
            'totalPengguna' => User::count(),
            'totalBuku' => Buku::count(),
            'totalPeminjaman' => Peminjaman::count(),
            'menunggu' => Peminjaman::where('status', Peminjaman::STATUS_MENUNGGU)->count(),
        ]);
    }

    public function petugas(): View
    {
        return view('dashboard-petugas', [
            'totalBuku' => Buku::count(),
            'menunggu' => Peminjaman::where('status', Peminjaman::STATUS_MENUNGGU)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
        ]);
    }
}