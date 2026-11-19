<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['name' => 'Admin Perpustakaan',  'email' => 'admin@perpus.test',   'role' => 'admin'],
            ['name' => 'Petugas Perpustakaan', 'email' => 'petugas@perpus.test', 'role' => 'petugas'],
            ['name' => 'Siswa Satu',          'email' => 'siswa1@perpus.test',  'role' => 'siswa'],
            ['name' => 'Siswa Dua',           'email' => 'siswa2@perpus.test',  'role' => 'siswa'],
        ];

        foreach ($akun as $a) {
            $password = match ($a['role']) {
                'admin' => Hash::make('AdminPerpus#2026!SecureKey'),
                default => Hash::make('password'),
            };

            $user = User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name' => $a['name'],
                    'password' => $password,
                ]
            );

            // Sinkronkan kolom "role" lama dengan role Spatie
            $user->forceFill(['role' => $a['role']])->save();
            $user->syncRoles([$a['role']]);

            // Siswa otomatis punya data anggota agar bisa meminjam
            if ($a['role'] === 'siswa') {
                Anggota::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'user_id' => $user->id,
                        'nama' => $user->name,
                        'no_telepon' => '081200000000',
                    ]
                );
            }
        }
    }
}