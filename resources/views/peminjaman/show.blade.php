<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Peminjaman #{{ $peminjaman->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">Anggota</dt>
                    <dd class="col-span-2">{{ $peminjaman->anggota->nama ?? '-' }}</dd>
                    <dt class="text-gray-500">Email Anggota</dt>
                    <dd class="col-span-2">{{ $peminjaman->anggota->email ?? '-' }}</dd>
                    <dt class="text-gray-500">Buku</dt>
                    <dd class="col-span-2 font-medium">{{ $peminjaman->buku->judul ?? '-' }}</dd>
                    <dt class="text-gray-500">Kategori</dt>
                    <dd class="col-span-2">{{ $peminjaman->buku->kategori->nama ?? '-' }}</dd>
                    <dt class="text-gray-500">Tanggal Pinjam</dt>
                    <dd class="col-span-2">{{ $peminjaman->tanggal_pinjam }}</dd>
                    <dt class="text-gray-500">Tanggal Kembali</dt>
                    <dd class="col-span-2">{{ $peminjaman->tanggal_kembali ?? '-' }}</dd>
                    <dt class="text-gray-500">Status</dt>
                    <dd class="col-span-2"><x-status-badge :status="$peminjaman->status" /></dd>
                </dl>

                <div class="mt-6">
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>