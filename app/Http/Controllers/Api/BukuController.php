<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * GET /api/buku?cari=&kategori=&tahun=&page=
     */
    public function index(Request $request): JsonResponse
    {
        $buku = Buku::query()
            ->with('kategori')                 // eager loading
            ->withCount('peminjaman')          // jumlah peminjaman per buku
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->query('cari');
                $q->where(function ($q) use ($cari) {
                    $q->where('judul', 'like', "%{$cari}%")
                      ->orWhere('penulis', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_id', $request->query('kategori')))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun_terbit', $request->query('tahun')))
            ->latest()                         // terbaru lebih dulu
            ->paginate(10)
            ->withQueryString();               // query string ikut terbawa

        return response()->json([
            'message' => 'Daftar buku berhasil diambil',
            'data'    => BukuResource::collection($buku->items()),
            'meta'    => [
                'current_page'  => $buku->currentPage(),
                'per_page'      => $buku->perPage(),
                'total'         => $buku->total(),
                'last_page'     => $buku->lastPage(),
                'next_page_url' => $buku->nextPageUrl(),
                'prev_page_url' => $buku->previousPageUrl(),
            ],
        ], 200);
    }

    /**
     * POST /api/buku
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        $buku = Buku::create($data);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data'    => new BukuResource($buku->load('kategori')),
        ], 201);
    }

    /**
     * GET /api/buku/{id}
     */
    public function show(string $id): JsonResponse
    {
        $buku = Buku::with(['kategori', 'peminjaman'])->find($id);

        if (! $buku) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json(['data' => new BukuResource($buku)], 200);
    }

    /**
     * PUT /api/buku/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $buku = Buku::find($id);

        if (! $buku) {
            return response()->json(['message' => 'not found'], 404);
        }

        $data = $request->validate($this->rules(), $this->messages());

        $buku->update($data);

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data'    => new BukuResource($buku->fresh('kategori')),
        ], 200);
    }

    /**
     * DELETE /api/buku/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $buku = Buku::find($id);

        if (! $buku) {
            return response()->json(['message' => 'not found'], 404);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }

    /**
     * Aturan validasi untuk tambah & ubah buku.
     */
    private function rules(): array
    {
        return [
            'kategori_id'  => ['required', 'integer', 'exists:kategoris,id'],
            'judul'        => ['required', 'string', 'max:150'],
            'penulis'      => ['required', 'string', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
        ];
    }

    private function messages(): array
    {
        return [
            'kategori_id.required'  => 'Kategori wajib dipilih.',
            'kategori_id.integer'   => 'Kategori tidak valid.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak ditemukan.',
            'judul.required'        => 'Judul wajib diisi.',
            'judul.max'             => 'Judul maksimal 150 karakter.',
            'penulis.required'      => 'Penulis wajib diisi.',
            'penulis.max'           => 'Penulis maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer'  => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.min'      => 'Tahun terbit minimal 1000.',
            'tahun_terbit.max'      => 'Tahun terbit tidak boleh melebihi tahun ini.',
        ];
    }
}