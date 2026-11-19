<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $semuaPermission = [
            'akses-dashboard-admin',               // 1
            'akses-dashboard-petugas',             // 2
            'lihat-daftar-buku',                   // 3
            'kelola-data-buku',                    // 4
            'kelola-data-pengguna',                // 5
            'kelola-role-pengguna',                // 6
            'kelola-peminjaman',                   // 7
            'kelola-pengembalian',                 // 8
            'ajukan-peminjaman-buku',              // 9
            'proses-persetujuan-peminjaman',       // 10
            'catat-pengembalian-buku',             // 11
            'lihat-status-peminjaman-sendiri',     // 12
            'lihat-riwayat-peminjaman-sendiri',    // 13
            'lihat-laporan-perpustakaan',          // 14
            'lihat-profil-sendiri',                // 15
            'ubah-profil-sendiri',                 // 16
        ];

        foreach ($semuaPermission as $nama) {
            Permission::firstOrCreate(['name' => $nama, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $petugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $siswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        // Admin: semua kecuali dashboard petugas
        $admin->syncPermissions([
            'akses-dashboard-admin',
            'lihat-daftar-buku',
            'kelola-data-buku',
            'kelola-data-pengguna',
            'kelola-role-pengguna',
            'kelola-peminjaman',
            'kelola-pengembalian',
            'ajukan-peminjaman-buku',
            'proses-persetujuan-peminjaman',
            'catat-pengembalian-buku',
            'lihat-status-peminjaman-sendiri',
            'lihat-riwayat-peminjaman-sendiri',
            'lihat-laporan-perpustakaan',
            'lihat-profil-sendiri',
            'ubah-profil-sendiri',
        ]);

        // Petugas: tanpa dashboard admin, kelola pengguna, dan kelola role
        $petugas->syncPermissions([
            'akses-dashboard-petugas',
            'lihat-daftar-buku',
            'kelola-data-buku',
            'kelola-peminjaman',
            'kelola-pengembalian',
            'ajukan-peminjaman-buku',
            'proses-persetujuan-peminjaman',
            'catat-pengembalian-buku',
            'lihat-status-peminjaman-sendiri',
            'lihat-riwayat-peminjaman-sendiri',
            'lihat-laporan-perpustakaan',
            'lihat-profil-sendiri',
            'ubah-profil-sendiri',
        ]);

        // Siswa: hanya fitur untuk dirinya sendiri
        $siswa->syncPermissions([
            'lihat-daftar-buku',
            'ajukan-peminjaman-buku',
            'lihat-status-peminjaman-sendiri',
            'lihat-riwayat-peminjaman-sendiri',
            'lihat-profil-sendiri',
            'ubah-profil-sendiri',
        ]);
    }
}