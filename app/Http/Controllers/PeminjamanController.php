<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
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

    public function index(): View|Response
    {
        $user = auth()->user();
        $query = Peminjaman::query();
        $ownerCol = $this->resolveOwnerColumn();

        if (! $this->isStaff($user) && $user && $ownerCol) {
            $query->where($ownerCol, $user->getAuthIdentifier());
        }

        $peminjamans = $query->get();

        if (view()->exists('riwayat.index')) {
            return response()->view('riwayat.index', [
                'peminjamans' => $peminjamans,
                'data' => $peminjamans,
            ], 200);
        }

        if (view()->exists('peminjaman.index')) {
            return response()->view('peminjaman.index', [
                'peminjamans' => $peminjamans,
                'data' => $peminjamans,
            ], 200);
        }

        $rows = '';
        foreach ($peminjamans as $item) {
            $id = e($item->getKey());
            $rows .= '<li><a href="' . e(url('/riwayat/' . $id)) . '">Peminjaman #' . $id . '</a></li>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Riwayat Peminjaman</title></head>'
            . '<body style="font-family:sans-serif;padding:24px;">'
            . '<h2>Daftar Riwayat Peminjaman</h2><ul>' . $rows . '</ul>'
            . '</body></html>';

        return response($html, 200);
    }

    public function show($peminjaman): View|Response
    {
        $model = $peminjaman instanceof Peminjaman
            ? $peminjaman
            : Peminjaman::query()->findOrFail($peminjaman);

        // Soal 4: Otorisasi detail peminjaman via PeminjamanPolicy@view
        Gate::authorize('view', $model);

        $payload = [
            'peminjaman' => $model,
            'riwayat' => $model,
            'detail' => $model,
        ];

        if (view()->exists('riwayat.show')) {
            return response()->view('riwayat.show', $payload, 200);
        }

        if (view()->exists('peminjaman.show')) {
            return response()->view('peminjaman.show', $payload, 200);
        }

        $attributes = $model->getAttributes();
        $items = '';
        foreach ($attributes as $key => $val) {
            $items .= '<tr><th style="text-align:left;padding:6px 12px;border:1px solid #ddd;">'
                . e($key) . '</th><td style="padding:6px 12px;border:1px solid #ddd;">'
                . e(is_scalar($val) || $val === null ? (string) ($val ?? '-') : json_encode($val))
                . '</td></tr>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Detail Peminjaman</title></head>'
            . '<body style="font-family:sans-serif;padding:24px;">'
            . '<h2>Detail Peminjaman #' . e($model->getKey()) . '</h2>'
            . '<table style="border-collapse:collapse;margin-top:12px;">' . $items . '</table>'
            . '<p style="margin-top:16px;"><a href="' . e(url('/riwayat')) . '">Kembali ke Riwayat</a></p>'
            . '</body></html>';

        return response($html, 200);
    }

    private function resolveOwnerColumn(): ?string
    {
        $sample = Peminjaman::query()->first();
        if (! $sample) {
            return 'anggota_id';
        }

        foreach (['anggota_id', 'user_id', 'siswa_id', 'id_user'] as $col) {
            if (array_key_exists($col, $sample->getAttributes())) {
                return $col;
            }
        }

        return null;
    }
}