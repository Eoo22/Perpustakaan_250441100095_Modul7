@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2>{{ $selectedBuku->judul }}</h2>
</div>

<div class="book-detail">
    <p><strong>Penulis:</strong> {{ $selectedBuku->penulis }}</p>
    <p><strong>Tahun:</strong> {{ $selectedBuku->tahun_terbit }}</p>
    <p><strong>Kategori:</strong> {{ $selectedBuku->kategori->nama ?? '-' }}</p>

    <a href="{{ url('/buku') }}">&larr; Kembali ke daftar</a>
</div>
@endsection