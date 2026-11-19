<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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

    private function kategoriList()
    {
        if (! class_exists(Kategori::class)) {
            return collect();
        }

        $kategoriModel = new Kategori();
        $table = $kategoriModel->getTable();

        if (! Schema::hasTable($table)) {
            return collect();
        }

        foreach (['nama', 'nama_kategori', 'name', 'judul'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                return Kategori::query()->orderBy($col)->get();
            }
        }

        return Kategori::query()->get();
    }

    private function kategoriColumn(string $table): ?string
    {
        foreach (['kategori_id', 'id_kategori', 'category_id'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                return $col;
            }
        }

        return null;
    }

    private function canManageBuku($user): bool
    {
        if (! $user) {
            return false;
        }

        try {
            if ($user->can('kelola-buku')) {
                return true;
            }
        } catch (\Throwable) {
            // Abaikan jika Gate belum terdaftar
        }

        $role = strtolower(trim((string) ($user->getAttribute('role') ?? '')));

        return in_array($role, ['admin', 'petugas'], true);
    }

    public function index(Request $request): View|Response
    {
        $keyword = trim((string) $request->query('q', ''));
        $table = $this->bukuTable();

        $query = Buku::query();

        if ($keyword !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
            $query->where(function ($sub) use ($escaped, $table) {
                $sub->where('judul', 'like', '%' . $escaped . '%');
                if (Schema::hasColumn($table, 'penulis')) {
                    $sub->orWhere('penulis', 'like', '%' . $escaped . '%');
                }
            });
        }

        $bukus = Schema::hasColumn($table, 'judul')
            ? $query->orderBy('judul')->get()
            : $query->latest()->get();

        $hasStok = Schema::hasColumn($table, 'stok');
        $hasPenulis = Schema::hasColumn($table, 'penulis');
        $canManage = $this->canManageBuku($request->user());
        $canPinjam = Route::has('peminjaman.store') && class_exists(\App\Http\Controllers\PeminjamanController::class);

        $rowsHtml = '';
        foreach ($bukus as $item) {
            $id = e($item->getKey());
            $judul = e((string) ($item->getAttribute('judul') ?? '-'));
            $penulis = e($hasPenulis ? (string) ($item->getAttribute('penulis') ?? '-') : '-');
            $stokVal = $hasStok ? (int) ($item->getAttribute('stok') ?? 0) : null;
            $stokText = $hasStok ? (string) $stokVal : '-';

            $aksiLinks = '<a href="' . e(route('buku.show', $item)) . '" style="color:#2563eb;text-decoration:none;font-weight:600;margin-right:10px;">Detail</a>';

            if ($canPinjam) {
                $disabledAttr = ($hasStok && $stokVal <= 0) ? ' disabled' : '';
                $btnStyle = ($hasStok && $stokVal <= 0)
                    ? 'background:#9ca3af;color:#fff;border:none;padding:5px 10px;border-radius:6px;cursor:not-allowed;font-size:12px;'
                    : 'background:#16a34a;color:#fff;border:none;padding:5px 10px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;';
                $btnLabel = ($hasStok && $stokVal <= 0) ? 'Habis' : 'Pinjam';

                $aksiLinks .= '<form action="' . e(route('peminjaman.store')) . '" method="POST" style="display:inline-block;margin-right:8px;">'
                    . csrf_field()
                    . '<input type="hidden" name="buku_id" value="' . $id . '">'
                    . '<button type="submit" style="' . $btnStyle . '"' . $disabledAttr . '>' . $btnLabel . '</button>'
                    . '</form>';
            }

            if ($canManage && Route::has('buku.edit')) {
                $aksiLinks .= '<a href="' . e(route('buku.edit', $item)) . '" style="color:#d97706;text-decoration:none;font-weight:600;margin-right:8px;">Edit</a>';
            }

            if ($canManage && Route::has('buku.destroy')) {
                $aksiLinks .= '<form action="' . e(route('buku.destroy', $item)) . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Hapus buku ini?\');">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" style="background:#dc2626;color:#fff;border:none;padding:5px 10px;border-radius:6px;cursor:pointer;font-size:12px;">Hapus</button>'
                    . '</form>';
            }

            $rowsHtml .= '<tr style="border-bottom:1px solid #e5e7eb;">'
                . '<td style="padding:12px 14px;">' . $judul . '</td>'
                . '<td style="padding:12px 14px;">' . $penulis . '</td>'
                . '<td style="padding:12px 14px;">' . e($stokText) . '</td>'
                . '<td style="padding:12px 14px;white-space:nowrap;">' . $aksiLinks . '</td>'
                . '</tr>';
        }

        if ($rowsHtml === '') {
            $rowsHtml = '<tr><td colspan="4" style="padding:16px;text-align:center;color:#6b7280;">Belum ada data buku.</td></tr>';
        }

        $viewErrorBag = $request->session()->get('errors');
        $flashHtml = '';
        if (session('success')) {
            $flashHtml .= '<div style="background:#dcfce7;color:#166534;padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid #86efac;">'
                . e(session('success')) . '</div>';
        }
        if (session('error')) {
            $flashHtml .= '<div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid #fca5a5;">'
                . e(session('error')) . '</div>';
        }
        if ($viewErrorBag && $viewErrorBag->any()) {
            $flashHtml .= '<div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid #fca5a5;">'
                . e($viewErrorBag->first()) . '</div>';
        }

        $tambahBtn = ($canManage && Route::has('buku.create'))
            ? '<a href="' . e(route('buku.create')) . '" style="background:#111827;color:#fff;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">+ Tambah Buku</a>'
            : '';

        $riwayatLink = Route::has('riwayat.index')
            ? '<a href="' . e(route('riwayat.index')) . '" style="color:#374151;text-decoration:none;font-weight:500;">Riwayat</a>'
            : '';

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Daftar Buku</title></head>'
            . '<body style="font-family:system-ui,-apple-system,sans-serif;background:#f9fafb;color:#111827;margin:0;">'
            . '<nav style="background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 28px;display:flex;justify-content:space-between;align-items:center;">'
            . '<div style="display:flex;gap:20px;align-items:center;">'
            . '<a href="' . e(url('/dashboard')) . '" style="color:#374151;text-decoration:none;">Dashboard</a>'
            . '<a href="' . e(route('buku.index')) . '" style="color:#111827;font-weight:700;text-decoration:none;">Daftar Buku</a>'
            . $riwayatLink
            . '</div>'
            . '<div style="font-size:14px;color:#374151;">' . e((string) ($request->user()?->name ?? '')) . '</div>'
            . '</nav>'
            . '<div style="max-width:1040px;margin:28px auto;padding:0 20px;">'
            . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">'
            . '<h1 style="margin:0;font-size:24px;">Daftar Buku</h1>'
            . $tambahBtn
            . '</div>'
            . $flashHtml
            . '<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">'
            . '<form method="GET" action="' . e(route('buku.index')) . '" style="display:flex;gap:10px;margin-bottom:18px;">'
            . '<input type="text" name="q" value="' . e($keyword) . '" placeholder="Cari judul buku..." style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;width:260px;">'
            . '<button type="submit" style="background:#1f2937;color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-weight:600;">Cari</button>'
            . '</form>'
            . '<table style="width:100%;border-collapse:collapse;text-align:left;">'
            . '<thead><tr style="border-bottom:2px solid #e5e7eb;color:#4b5563;font-size:13px;">'
            . '<th style="padding:10px 14px;">Judul</th>'
            . '<th style="padding:10px 14px;">Penulis</th>'
            . '<th style="padding:10px 14px;">Stok</th>'
            . '<th style="padding:10px 14px;">Aksi</th>'
            . '</tr></thead>'
            . '<tbody>' . $rowsHtml . '</tbody>'
            . '</table>'
            . '</div></div></body></html>';

        return response($html, 200);
    }

    // 2b: Query menggunakan parameter binding sehingga payload SQLi tidak mengeksekusi logika OR
    public function cari(Request $request): JsonResponse
    {
        $judul = (string) $request->query('judul', '');
        $table = $this->bukuTable();

        $hasil = DB::select("SELECT * FROM {$table} WHERE judul = ?", [$judul]);

        return response()->json($hasil);
    }

    public function show(Buku $buku): View|Response
    {
        $table = $this->bukuTable();
        $hasStok = Schema::hasColumn($table, 'stok');
        $stokVal = $hasStok ? (int) ($buku->getAttribute('stok') ?? 0) : null;
        $canPinjam = Route::has('peminjaman.store') && class_exists(\App\Http\Controllers\PeminjamanController::class);

        $pinjamForm = '';
        if ($canPinjam) {
            $disabledAttr = ($hasStok && $stokVal <= 0) ? ' disabled' : '';
            $btnText = ($hasStok && $stokVal <= 0) ? 'Stok Habis' : 'Pinjam Buku Ini';
            $pinjamForm = '<form action="' . e(route('peminjaman.store')) . '" method="POST" style="margin-top:18px;">'
                . csrf_field()
                . '<input type="hidden" name="buku_id" value="' . e($buku->getKey()) . '">'
                . '<button type="submit" style="background:#16a34a;color:#fff;border:none;padding:10px 18px;border-radius:8px;cursor:pointer;font-weight:600;"' . $disabledAttr . '>'
                . $btnText . '</button></form>';
        }

        $rows = '';
        foreach ($buku->getAttributes() as $key => $val) {
            $rows .= '<tr><th style="text-align:left;padding:8px 12px;border:1px solid #e5e7eb;background:#f9fafb;">'
                . e($key) . '</th><td style="padding:8px 12px;border:1px solid #e5e7eb;">'
                . e(is_scalar($val) || $val === null ? (string) ($val ?? '-') : json_encode($val))
                . '</td></tr>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Detail Buku</title></head>'
            . '<body style="font-family:system-ui,-apple-system,sans-serif;background:#f9fafb;color:#111827;margin:0;">'
            . '<div style="max-width:760px;margin:32px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;">'
            . '<h1 style="margin-top:0;font-size:22px;">' . e((string) ($buku->getAttribute('judul') ?? 'Detail Buku')) . '</h1>'
            . '<table style="width:100%;border-collapse:collapse;margin-top:14px;">' . $rows . '</table>'
            . $pinjamForm
            . '<div style="margin-top:18px;display:flex;gap:16px;">'
            . '<a href="' . e(route('buku.index')) . '" style="color:#2563eb;text-decoration:none;">Kembali ke Daftar Buku</a>'
            . (Route::has('riwayat.index') ? '<a href="' . e(route('riwayat.index')) . '" style="color:#374151;text-decoration:none;">Lihat Riwayat</a>' : '')
            . '</div></div></body></html>';

        return response($html, 200);
    }

    public function create(): View
    {
        $kategoris = $this->kategoriList();

        return view('buku.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $table = $this->bukuTable();
        $katCol = $this->kategoriColumn($table);

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
        ];

        if ($katCol !== null) {
            $rules[$katCol] = ['nullable', 'integer'];
        }
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

        if ($katCol !== null && empty($validated[$katCol])) {
            $firstKategori = $this->kategoriList()->first();
            if ($firstKategori) {
                $validated[$katCol] = $firstKategori->getKey();
            }
        }

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
        $kategoris = $this->kategoriList();

        return view('buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $table = $this->bukuTable();
        $katCol = $this->kategoriColumn($table);

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
        ];

        if ($katCol !== null) {
            $rules[$katCol] = ['nullable', 'integer'];
        }
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

        if ($katCol !== null && ! isset($validated[$katCol])) {
            $validated[$katCol] = $buku->getAttribute($katCol);
        }
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