<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $bukus = Buku::with('kategori')
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->query('cari');
                $q->where(function ($q) use ($cari) {
                    $q->where('judul', 'like', "%{$cari}%")
                        ->orWhere('penulis', 'like', "%{$cari}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('buku.index', compact('bukus'));
    }

    /**
     * CELAH A2 (SEMENTARA untuk praktikum): input digabungkan langsung ke SQL.
     * GET /buku-cari?judul=...
     */
    public function cari(Request $request): JsonResponse
    {
        $judul = $request->query('judul');

        $hasil = DB::select("SELECT * FROM bukus WHERE judul = '$judul'");

        return response()->json($hasil);
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('buku.show', compact('buku'));
    }

    public function create(): View
    {
        return view('buku.create', [
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Buku::create($request->validate($this->rules()));

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku.edit', [
            'buku' => $buku,
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validate($this->rules()));

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'judul' => ['required', 'string', 'max:150'],
            'penulis' => ['required', 'string', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
        ];
    }
}