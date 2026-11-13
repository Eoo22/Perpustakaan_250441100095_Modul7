<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => 'required',
            'judul'       => 'required',
            'penulis'     => 'required',
            'tahun_terbit'=> 'required',
        ];
    }
}