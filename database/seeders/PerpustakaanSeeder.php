<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PerpustakaanSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Kategori =====
        $namaKategori = ['Pemrograman', 'Basis Data', 'Jaringan Komputer', 'Sistem Informasi'];

        $kategori = [];
        foreach ($namaKategori as $nama) {
            $kategori[$nama] = Kategori::firstOrCreate(['nama' => $nama]);
        }

        // ===== Buku =====
        $daftarBuku = [
            ['Pemrograman Laravel Dasar', 'Edi Putra', 2024, 'Pemrograman'],
            ['Belajar PHP untuk Pemula', 'Rina Sari', 2022, 'Pemrograman'],
            ['Dasar-Dasar JavaScript', 'Budi Santoso', 2023, 'Pemrograman'],
            ['Basis Data MySQL', 'Ahmad Fauzi', 2021, 'Basis Data'],
            ['Perancangan Basis Data', 'Dewi Lestari', 2020, 'Basis Data'],
            ['Jaringan Komputer Modern', 'Hendra Wijaya', 2022, 'Jaringan Komputer'],
            ['Analisis dan Perancangan Sistem', 'Siti Aminah', 2023, 'Sistem Informasi'],
            ['Manajemen Sistem Informasi', 'Agus Pratama', 2021, 'Sistem Informasi'],
        ];

        $buku = [];
        foreach ($daftarBuku as [$judul, $penulis, $tahun, $namaKat]) {
            $buku[$judul] = Buku::firstOrCreate(
                ['judul' => $judul],
                [
                    'kategori_id' => $kategori[$namaKat]->id,
                    'penulis' => $penulis,
                    'tahun_terbit' => $tahun,
                ]
            );
        }

        // ===== Contoh peminjaman (butuh anggota siswa dari UserSeeder) =====
        $siswa1 = Anggota::where('email', 'siswa1@perpus.test')->first();
        $siswa2 = Anggota::where('email', 'siswa2@perpus.test')->first();

        if (! $siswa1 || ! $siswa2) {
            $this->command?->warn('Anggota siswa belum ada. Jalankan UserSeeder lebih dulu.');
            return;
        }

        $contoh = [
            // anggota, judul buku, tanggal pinjam, tanggal kembali, status
            [$siswa1, 'Pemrograman Laravel Dasar', now()->subDays(10), now()->subDays(3), Peminjaman::STATUS_DIKEMBALIKAN],
            [$siswa1, 'Basis Data MySQL', now()->subDays(2), null, Peminjaman::STATUS_DIPINJAM],
            [$siswa1, 'Belajar PHP untuk Pemula', now(), null, Peminjaman::STATUS_MENUNGGU],
            [$siswa2, 'Jaringan Komputer Modern', now()->subDays(5), null, Peminjaman::STATUS_DIPINJAM],
            [$siswa2, 'Dasar-Dasar JavaScript', now()->subDay(), null, Peminjaman::STATUS_MENUNGGU],
        ];

        foreach ($contoh as [$anggota, $judul, $pinjam, $kembali, $status]) {
            Peminjaman::firstOrCreate(
                [
                    'anggota_id' => $anggota->id,
                    'buku_id' => $buku[$judul]->id,
                    'status' => $status,
                ],
                [
                    'tanggal_pinjam' => $pinjam->toDateString(),
                    'tanggal_kembali' => $kembali?->toDateString(),
                ]
            );
        }
    }
}