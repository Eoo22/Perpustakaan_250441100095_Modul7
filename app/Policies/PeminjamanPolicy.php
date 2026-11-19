<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Soal 4: Menutup Celah A4 dengan Policy.
     * Admin dan petugas boleh melihat semua peminjaman,
     * sedangkan siswa hanya boleh melihat peminjaman miliknya sendiri.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        // Admin & petugas boleh melihat semua peminjaman
        if ($user->can('kelola-peminjaman')
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'petugas']))
            || in_array($user->role, ['admin', 'petugas'])) {
            return true;
        }

        // Siswa hanya boleh melihat peminjaman miliknya sendiri
        $ownerId = $peminjaman->anggota?->user_id ?? $peminjaman->user_id;
        return $ownerId !== null && $ownerId === $user->id;
    }
}
