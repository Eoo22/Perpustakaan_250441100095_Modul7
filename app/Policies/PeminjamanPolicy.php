<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        if ($this->isStaff($user)) {
            return true;
        }

        $userId = $user->getAuthIdentifier();
        if ($userId === null) {
            return false;
        }

        $anggotaModel = $peminjaman->getAttribute('anggota');
        $userModel = $peminjaman->getAttribute('user');
        $siswaModel = $peminjaman->getAttribute('siswa');

        $ownerCandidates = [
            $peminjaman->getAttribute('anggota_id'),
            $peminjaman->getAttribute('user_id'),
            $peminjaman->getAttribute('siswa_id'),
            $peminjaman->getAttribute('id_user'),
            $peminjaman->getAttribute('member_id'),
            $peminjaman->getAttribute('peminjam_id'),
            is_object($anggotaModel) && method_exists($anggotaModel, 'getKey') ? $anggotaModel->getKey() : null,
            is_object($userModel) && method_exists($userModel, 'getKey') ? $userModel->getKey() : null,
            is_object($siswaModel) && method_exists($siswaModel, 'getKey') ? $siswaModel->getKey() : null,
        ];

        foreach ($ownerCandidates as $ownerId) {
            if ($ownerId !== null && $ownerId !== '' && (string) $ownerId === (string) $userId) {
                return true;
            }
        }

        return false;
    }

    private function isStaff(User $user): bool
    {
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
}