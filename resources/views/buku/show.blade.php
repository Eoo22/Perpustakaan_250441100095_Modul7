<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Buku: {{ $buku->judul }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <dl class="grid grid-cols-3 gap-y-3 text-sm">
                    <dt class="text-gray-500">Judul</dt>
                    <dd class="col-span-2 font-medium">{{ $buku->judul }}</dd>

                    <dt class="text-gray-500">Penulis</dt>
                    <dd class="col-span-2">{{ $buku->penulis ?? '-' }}</dd>

                    <dt class="text-gray-500">Kategori</dt>
                    <dd class="col-span-2">{{ $buku->kategori->nama ?? '-' }}</dd>

                    <dt class="text-gray-500">Deskripsi</dt>
                    <dd class="col-span-2">{{ $buku->deskripsi ?? '-' }}</dd>
                </dl>

                @can('kelola-data-buku')
                    <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                            Hapus Buku
                        </button>
                    </form>
                @endcan

                <div>
                    <a href="{{ route('buku.index') }}" class="text-sm text-gray-600 hover:underline">
                        Kembali ke Daftar Buku
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>