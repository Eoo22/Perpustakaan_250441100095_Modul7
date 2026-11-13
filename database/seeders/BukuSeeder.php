<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Buku::truncate();
        Schema::enableForeignKeyConstraints();

        $kategori = [];
        foreach (['Teknologi', 'Ilmu Komputer', 'Desain'] as $nama) {
            $kategori[$nama] = Kategori::firstOrCreate(['nama' => $nama])->id;
        }

        $data = [
            ['judul' => 'Belajar Pemrograman Laravel', 'penulis' => 'Andi Wijaya', 'tahun_terbit' => 2024, 'kategori_id' => $kategori['Teknologi']],
            ['judul' => 'Algoritma dan Struktur Data', 'penulis' => 'Budi Santoso', 'tahun_terbit' => 2023, 'kategori_id' => $kategori['Ilmu Komputer']],
            ['judul' => 'Dasar-Dasar Desain Web', 'penulis' => 'Siti Rahma', 'tahun_terbit' => 2022, 'kategori_id' => $kategori['Desain']],
            ['judul' => 'Basis Data Relasional', 'penulis' => 'Dewi Lestari', 'tahun_terbit' => 2023, 'kategori_id' => $kategori['Teknologi']],
            ['judul' => 'Kecerdasan Buatan untuk Pemula', 'penulis' => 'Ahmad Fauzi', 'tahun_terbit' => 2025, 'kategori_id' => $kategori['Teknologi']],
        ];

        foreach ($data as $buku) {
            Buku::create($buku);
        }
    }
}