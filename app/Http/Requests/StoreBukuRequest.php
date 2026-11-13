<?php

namespace App\Http\Requests;// sesuaikan namespace project Anda

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Diperbolehkan
    }

    public function rules(): array
    {
        // Mendapatkan tahun saat ini secara dinamis (2026)
        $tahunSekarang = date('Y'); 

        return [
            'kategori_id'  => 'required|exists:kategoris,id',
            'judul'        => 'required|string|min:3|max:150',
            'penulis'      => 'required|string|min:3|max:100',
            'tahun_terbit' => "required|integer|min:1900|max:{$tahunSekarang}",
        ];
    }

    public function messages(): array
    {
        $tahunSekarang = date('Y');

        return [
            'kategori_id.required'  => 'Kategori wajib diisi.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.string'          => 'Judul buku harus berupa teks.',
            'judul.min'             => 'Judul buku minimal 3 karakter.',
            'judul.max'             => 'Judul buku maksimal 150 karakter.',
            'penulis.required'      => 'Penulis wajib diisi.',
            'penulis.string'        => 'Penulis harus berupa teks.',
            'penulis.min'           => 'Penulis minimal 3 karakter.',
            'penulis.max'           => 'Penulis maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer'  => 'Tahun terbit harus berupa bilangan bulat.',
            'tahun_terbit.min'      => 'Tahun terbit minimal tahun 1900.',
            'tahun_terbit.max'      => "Tahun terbit harus antara 1900 sampai {$tahunSekarang}.",
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_id'  => 'kategori',
            'tahun_terbit' => 'tahun terbit',
        ];
    }
}