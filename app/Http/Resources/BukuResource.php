<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Pemetaan kolom database (tabel bukus) -> atribut respons API:
 *
 *   kategori_id  (bukus)     -> kategori_id  (tetap, dipakai form ubah)
 *   kategoris.nama (relasi)  -> kategori     (nama kategori, bukan id)
 *   judul                    -> judul
 *   penulis                  -> penulis
 *   tahun_terbit             -> tahun        (nama atribut berbeda dari kolom)
 */
class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'kategori'          => $this->kategori?->nama,
            'kategori_id'       => $this->kategori_id,
            'judul'             => $this->judul,
            'penulis'           => $this->penulis,
            'tahun'             => $this->tahun_terbit,
            'jumlah_peminjaman' => $this->whenCounted('peminjaman'),
            'peminjaman'        => $this->whenLoaded('peminjaman'),
        ];
    }
}
