<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        $isStaff = (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'petugas']))
            || in_array($user->role ?? null, ['admin', 'petugas'], true);

        if ($isStaff) {
            return true;
        }

        return (int) ($peminjaman->user_id ?? 0) === (int) $user->id;
    }
}