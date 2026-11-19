<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Buku</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">Judul</dt>
                    <dd class="col-span-2 font-medium">{{ $buku->judul }}</dd>
                    <dt class="text-gray-500">Penulis</dt>
                    <dd class="col-span-2">{{ $buku->penulis }}</dd>
                    <dt class="text-gray-500">Kategori</dt>
                    <dd class="col-span-2">{{ $buku->kategori->nama ?? '-' }}</dd>
                    <dt class="text-gray-500">Tahun Terbit</dt>
                    <dd class="col-span-2">{{ $buku->tahun_terbit }}</dd>
                </dl>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('buku.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                    @can('ajukan-peminjaman-buku')
                        <a href="{{ route('peminjaman.create', ['buku' => $buku->id]) }}" class="text-sm text-green-600 hover:underline">Ajukan Pinjam</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>