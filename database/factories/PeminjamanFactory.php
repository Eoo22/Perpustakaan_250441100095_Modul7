<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    public function definition(): array
    {
        $tanggalPinjam = $this->faker->dateTimeBetween('-1 month', 'now');
        $tanggalKembali = $this->faker->optional(0.7)->dateTimeBetween($tanggalPinjam, 'now');

        return [
            'anggota_id' => Anggota::inRandomOrder()->first()->id ?? Anggota::factory(),
            'buku_id' => Buku::inRandomOrder()->first()->id ?? Buku::factory(),
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
        ];
    }
}