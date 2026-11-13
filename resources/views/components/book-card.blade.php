@props(['buku'])

<div class="book-card">
    <h3>{{ $buku->judul }}</h3>
    <p><strong>Penulis:</strong> {{ $buku->penulis }}</p>
    <p><strong>Tahun:</strong> {{ $buku->tahun_terbit }}</p>
    <p><strong>Kategori:</strong> {{ $buku->kategori->nama ?? '-' }}</p>

    {{ $slot }}

    <a href="{{ url('/buku/' . $buku->id) }}">Lihat detail</a>
</div>