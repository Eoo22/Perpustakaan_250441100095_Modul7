<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Admin dan petugas boleh melihat semua peminjaman,
     * sedangkan siswa hanya boleh melihat peminjaman miliknya sendiri.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        if ($user->can('kelola-peminjaman')
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'petugas']))
            || in_array($user->role, ['admin', 'petugas'])) {
            return true;
        }

        $ownerId = $peminjaman->anggota?->user_id ?? $peminjaman->user_id;

        return $ownerId !== null && $ownerId === $user->id;
    }
}