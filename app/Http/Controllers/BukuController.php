<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BukuController extends Controller
{
    private function bukuTable(): string
    {
        if (Schema::hasTable('bukus')) {
            return 'bukus';
        }

        if (Schema::hasTable('buku')) {
            return 'buku';
        }

        return (new Buku())->getTable();
    }

    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $table = $this->bukuTable();

        $query = Buku::query();

        if ($keyword !== '') {
            $escaped = addcslashes($keyword, '%_\\');
            $query->where(function ($sub) use ($escaped, $table) {
                $sub->where('judul', 'like', "%{$escaped}%");
                if (Schema::hasColumn($table, 'penulis')) {
                    $sub->orWhere('penulis', 'like', "%{$escaped}%");
                }
            });
        }

        $bukus = Schema::hasColumn($table, 'judul')
            ? $query->orderBy('judul')->get()
            : $query->latest()->get();

        return view('buku.index', compact('bukus', 'keyword'));
    }

    // 2b: Query menggunakan parameter binding sehingga payload SQLi tidak mengeksekusi logika OR
    public function cari(Request $request): JsonResponse
    {
        $judul = (string) $request->query('judul', '');
        $table = $this->bukuTable();

        $hasil = DB::select("SELECT * FROM {$table} WHERE judul = ?", [$judul]);

        return response()->json($hasil);
    }

    public function show(Buku $buku): View
    {
        return view('buku.show', compact('buku'));
    }

    public function create(): View
    {
        return view('buku.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $table = $this->bukuTable();

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
        ];

        if (Schema::hasColumn($table, 'penulis')) {
            $rules['penulis'] = ['nullable', 'string', 'max:255'];
        }
        if (Schema::hasColumn($table, 'tahun_terbit')) {
            $rules['tahun_terbit'] = ['nullable', 'integer', 'min:1000', 'max:2100'];
        }
        if (Schema::hasColumn($table, 'stok')) {
            $rules['stok'] = ['nullable', 'integer', 'min:0'];
        }
        if (Schema::hasColumn($table, 'deskripsi')) {
            $rules['deskripsi'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        if (Schema::hasColumn($table, 'tahun_terbit') && ! isset($validated['tahun_terbit'])) {
            $validated['tahun_terbit'] = (int) date('Y');
        }
        if (Schema::hasColumn($table, 'stok') && ! isset($validated['stok'])) {
            $validated['stok'] = 1;
        }

        Buku::create($validated);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku.edit', compact('buku'));
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $table = $this->bukuTable();

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
        ];

        if (Schema::hasColumn($table, 'penulis')) {
            $rules['penulis'] = ['nullable', 'string', 'max:255'];
        }
        if (Schema::hasColumn($table, 'tahun_terbit')) {
            $rules['tahun_terbit'] = ['nullable', 'integer', 'min:1000', 'max:2100'];
        }
        if (Schema::hasColumn($table, 'stok')) {
            $rules['stok'] = ['nullable', 'integer', 'min:0'];
        }
        if (Schema::hasColumn($table, 'deskripsi')) {
            $rules['deskripsi'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        if (Schema::hasColumn($table, 'tahun_terbit') && ! isset($validated['tahun_terbit'])) {
            $validated['tahun_terbit'] = $buku->tahun_terbit ?? (int) date('Y');
        }
        if (Schema::hasColumn($table, 'stok') && ! isset($validated['stok'])) {
            $validated['stok'] = $buku->stok ?? 1;
        }

        $buku->update($validated);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    // 2c: Hapus buku via DELETE
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}