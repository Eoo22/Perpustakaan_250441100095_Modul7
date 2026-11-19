<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    private function isStaff($user): bool
    {
        if (! $user) {
            return false;
        }

        $role = strtolower(trim((string) ($user->getAttribute('role') ?? '')));
        if (in_array($role, ['admin', 'petugas'], true)) {
            return true;
        }

        if (method_exists($user, 'hasAnyRole')) {
            try {
                if ($user->hasAnyRole(['admin', 'petugas', 'Admin', 'Petugas'])) {
                    return true;
                }
            } catch (\Throwable) {
                // Abaikan jika role/guard belum terdaftar
            }
        }

        return false;
    }

    private function resolveOwnerColumn(): ?string
    {
        $table = (new Peminjaman())->getTable();

        foreach (['anggota_id', 'user_id', 'siswa_id', 'id_user', 'member_id', 'peminjam_id'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                return $col;
            }
        }

        return null;
    }

    private function resolveOwnerValue($user, ?string $ownerCol)
    {
        if (! $user || ! $ownerCol) {
            return null;
        }

        $userId = $user->getAuthIdentifier();

        if ($ownerCol === 'anggota_id' && class_exists(Anggota::class)) {
            $anggotaModel = new Anggota();
            $anggotaTable = $anggotaModel->getTable();

            if (Schema::hasTable($anggotaTable)) {
                foreach (['user_id', 'id_user'] as $fk) {
                    if (Schema::hasColumn($anggotaTable, $fk)) {
                        $anggota = Anggota::query()->where($fk, $userId)->first();
                        if ($anggota) {
                            return $anggota->getKey();
                        }
                    }
                }

                if (Schema::hasColumn($anggotaTable, 'email') && ! empty($user->email)) {
                    $anggota = Anggota::query()->where('email', $user->email)->first();
                    if ($anggota) {
                        return $anggota->getKey();
                    }
                }
            }
        }

        return $userId;
    }

    public function index(): View|Response
    {
        $user = auth()->user();
        $query = Peminjaman::query();
        $ownerCol = $this->resolveOwnerColumn();

        if (! $this->isStaff($user) && $user && $ownerCol) {
            $ownerVal = $this->resolveOwnerValue($user, $ownerCol);
            if ($ownerVal !== null) {
                $query->where($ownerCol, $ownerVal);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $peminjamans = $query->get();

        $rowsHtml = '';
        foreach ($peminjamans as $item) {
            $id = e($item->getKey());
            $detailUrl = e(url('/peminjaman/' . $id));
            $attrs = $item->getAttributes();

            $infoParts = [];
            foreach (['buku_id', 'id_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status'] as $field) {
                if (array_key_exists($field, $attrs) && $attrs[$field] !== null && $attrs[$field] !== '') {
                    $infoParts[] = e($field) . ': ' . e((string) $attrs[$field]);
                }
            }
            $summary = $infoParts ? ' (' . implode(' | ', $infoParts) . ')' : '';

            $rowsHtml .= '<li style="margin-bottom:8px;">'
                . '<a href="' . $detailUrl . '" style="color:#2563eb;text-decoration:none;font-weight:600;">Peminjaman #' . $id . '</a>'
                . '<span style="color:#4b5563;font-size:14px;">' . $summary . '</span>'
                . '</li>';
        }

        if ($rowsHtml === '') {
            $rowsHtml = '<li style="color:#6b7280;">Belum ada riwayat peminjaman.</li>';
        }

        $flashHtml = '';
        if (session('success')) {
            $flashHtml .= '<div style="background:#dcfce7;color:#166534;padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid #86efac;">'
                . e(session('success')) . '</div>';
        }
        if (session('error')) {
            $flashHtml .= '<div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid #fca5a5;">'
                . e(session('error')) . '</div>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Daftar Riwayat Peminjaman</title></head>'
            . '<body style="font-family:system-ui,-apple-system,sans-serif;background:#f9fafb;color:#111827;margin:0;">'
            . '<nav style="background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 28px;display:flex;justify-content:space-between;align-items:center;">'
            . '<div style="display:flex;gap:20px;align-items:center;">'
            . '<a href="' . e(url('/dashboard')) . '" style="color:#374151;text-decoration:none;">Dashboard</a>'
            . '<a href="' . e(route('buku.index')) . '" style="color:#374151;text-decoration:none;">Daftar Buku</a>'
            . '<a href="' . e(route('riwayat.index')) . '" style="color:#111827;font-weight:700;text-decoration:none;">Riwayat</a>'
            . '</div>'
            . '<div style="font-size:14px;color:#374151;">' . e((string) ($user?->name ?? '')) . '</div>'
            . '</nav>'
            . '<div style="max-width:860px;margin:28px auto;padding:0 20px;">'
            . '<h1 style="margin:0 0 16px;font-size:24px;">Daftar Riwayat Peminjaman</h1>'
            . $flashHtml
            . '<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">'
            . '<ul style="margin:0;padding-left:20px;">' . $rowsHtml . '</ul>'
            . '</div>'
            . '<div style="margin-top:16px;"><a href="' . e(route('buku.index')) . '" style="color:#2563eb;text-decoration:none;">Kembali ke Daftar Buku</a></div>'
            . '</div></body></html>';

        return response($html, 200);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'buku_id' => ['required', 'integer'],
        ]);

        $buku = Buku::query()->findOrFail($validated['buku_id']);
        $bukuTable = (new Buku())->getTable();
        $table = (new Peminjaman())->getTable();

        $payload = [];
        $ownerCol = $this->resolveOwnerColumn();
        if ($ownerCol) {
            $ownerVal = $this->resolveOwnerValue($user, $ownerCol);
            if ($ownerVal !== null) {
                $payload[$ownerCol] = $ownerVal;
            }
        }

        foreach (['buku_id', 'id_buku'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $payload[$col] = $buku->getKey();
                break;
            }
        }

        if (Schema::hasColumn($table, 'tanggal_pinjam')) {
            $payload['tanggal_pinjam'] = now()->toDateString();
        }
        if (Schema::hasColumn($table, 'tanggal_kembali')) {
            $payload['tanggal_kembali'] = now()->addDays(7)->toDateString();
        }
        if (Schema::hasColumn($table, 'status')) {
            $payload['status'] = 'dipinjam';
        }

        if (Schema::hasColumn($bukuTable, 'stok')) {
            $currentStok = (int) ($buku->getAttribute('stok') ?? 0);
            if ($currentStok <= 0) {
                return redirect()->route('buku.index')->with('error', 'Stok buku habis.');
            }
        }

        DB::transaction(function () use ($bukuTable, $buku, $payload) {
            if (Schema::hasColumn($bukuTable, 'stok')) {
                $buku->decrement('stok');
            }

            Peminjaman::query()->create($payload);
        });

        return redirect()->route('riwayat.index')->with('success', 'Buku berhasil dipinjam.');
    }

    public function show($peminjaman): View|Response
    {
        $model = $peminjaman instanceof Peminjaman
            ? $peminjaman
            : Peminjaman::query()->findOrFail($peminjaman);

        // Soal 4: Otorisasi detail peminjaman via PeminjamanPolicy@view
        Gate::authorize('view', $model);

        $attributes = $model->getAttributes();
        $items = '';
        foreach ($attributes as $key => $val) {
            $items .= '<tr><th style="text-align:left;padding:8px 12px;border:1px solid #e5e7eb;background:#f9fafb;">'
                . e($key) . '</th><td style="padding:8px 12px;border:1px solid #e5e7eb;">'
                . e(is_scalar($val) || $val === null ? (string) ($val ?? '-') : json_encode($val))
                . '</td></tr>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Detail Peminjaman</title></head>'
            . '<body style="font-family:system-ui,-apple-system,sans-serif;background:#f9fafb;color:#111827;margin:0;">'
            . '<div style="max-width:760px;margin:32px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;">'
            . '<h1 style="margin-top:0;font-size:22px;">Detail Peminjaman #' . e($model->getKey()) . '</h1>'
            . '<table style="width:100%;border-collapse:collapse;margin-top:14px;">' . $items . '</table>'
            . '<div style="margin-top:18px;"><a href="' . e(route('riwayat.index')) . '" style="color:#2563eb;text-decoration:none;">Kembali ke Riwayat</a></div>'
            . '</div></body></html>';

        return response($html, 200);
    }
}