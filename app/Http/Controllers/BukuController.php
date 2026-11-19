<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));

        $bukus = Buku::with('kategori')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $escaped = addcslashes($keyword, '%_\\');
                $query->where(function ($sub) use ($escaped) {
                    $sub->where('judul', 'like', "%{$escaped}%")
                        ->orWhere('penulis', 'like', "%{$escaped}%");
                });
            })
            ->orderBy('judul')
            ->get();

        return view('buku.index', compact('bukus', 'keyword'));
    }

    // A2: Pencarian aman menggunakan parameter binding (tanpa string SQL mentah)
    public function cari(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));

        $bukus = Buku::with('kategori')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $escaped = addcslashes($keyword, '%_\\');
                $query->where(function ($sub) use ($escaped) {
                    $sub->where('judul', 'like', "%{$escaped}%")
                        ->orWhere('penulis', 'like', "%{$escaped}%");
                });
            })
            ->orderBy('judul')
            ->get();

        return view('buku.index', compact('bukus', 'keyword'));
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('buku.show', compact('buku'));
    }

    public function create(): View
    {
        return view('buku.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Buku::create($validated);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku.edit', compact('buku'));
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    // A3: Hapus buku hanya lewat DELETE dengan proteksi route + @csrf + @method('DELETE')
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}