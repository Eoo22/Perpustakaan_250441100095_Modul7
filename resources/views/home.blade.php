@extends('layouts.app')

@section('content')
<div class="card-welcome">
    <h2>Selamat Datang di Sistem Perpustakaan</h2>
    <p>Aplikasi sederhana untuk mengelola dan melihat daftar koleksi buku perpustakaan digital.</p>
    <a href="{{ route('buku.index') }}" class="btn">Lihat Daftar Buku</a>
</div>
@endsection