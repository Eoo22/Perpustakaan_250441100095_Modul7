<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Role dan permission harus ada lebih dulu
        $this->call(RolePermissionSeeder::class);

        // 1. Buat user siswa
        $siswa = User::updateOrCreate(
            ['email' => 'siswa@perpus.test'],
            [
                'name' => 'Siswa Test (Deo)',
                'password' => Hash::make('password'),
            ]
        );
        $siswa->forceFill(['role' => 'siswa'])->save();
        $siswa->syncRoles(['siswa']);

        // 2. Buat user admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@perpus.test'],
            [
                'name' => 'Admin Perpustakaan',
                'password' => Hash::make('AdminPerpus#2026!SecureKey'),
            ]
        );
        $admin->forceFill(['role' => 'admin'])->save();
        $admin->syncRoles(['admin']);

        // 3. Kategori
        DB::table('kategoris')->updateOrInsert(
            ['id' => 1],
            [
                'nama'       => 'Pemrograman',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Buku
        DB::table('bukus')->updateOrInsert(
            ['id' => 1],
            [
                'kategori_id'  => 1,
                'judul'        => 'Pemrograman Laravel Dasar',
                'penulis'      => 'Edi Putra',
                'tahun_terbit' => 2024,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        // 5. Anggota (kolom yang tidak ada di tabel otomatis dilewati)
        $dataAnggota = [
            'user_id'    => $siswa->id,
            'nama'       => 'Siswa Test (Deo)',
            'nis'        => '12345',
            'kelas'      => 'XII',
            'alamat'     => 'Bandung',
            'no_telepon' => '081234567890',
            'email'      => 'siswa@perpus.test',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $dataAnggota = array_filter(
            $dataAnggota,
            fn ($nilai, $kolom) => Schema::hasColumn('anggotas', $kolom),
            ARRAY_FILTER_USE_BOTH
        );

        DB::table('anggotas')->updateOrInsert(['id' => 1], $dataAnggota);

        // 6. Peminjaman
        DB::table('peminjamans')->updateOrInsert(
            ['id' => 1],
            [
                'anggota_id'     => 1,
                'buku_id'        => 1,
                'tanggal_pinjam' => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        // 7. Akun petugas, siswa tambahan, kategori/buku/peminjaman contoh
        $this->call([
            UserSeeder::class,
            PerpustakaanSeeder::class,
        ]);
    }
}