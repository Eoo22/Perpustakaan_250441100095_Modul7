<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Buku</h2>
            @can('kelola-data-buku')
                <a href="{{ route('buku.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white hover:bg-gray-700">Tambah Buku</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('buku.index') }}" class="mb-4 flex gap-2">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari judul atau penulis"
                           class="w-full rounded-md border-gray-300 shadow-sm sm:w-72">
                    <button class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">Cari</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-2">Judul</th>
                                <th class="px-4 py-2">Penulis</th>
                                <th class="px-4 py-2">Kategori</th>
                                <th class="px-4 py-2">Tahun</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($bukus as $buku)
                                <tr>
                                    {{-- CELAH A1: tanpa escaping (SEMENTARA untuk praktikum) --}}
                                    <td class="px-4 py-2">{!! $buku->judul !!}</td>
                                    <td class="px-4 py-2">{{ $buku->penulis }}</td>
                                    <td class="px-4 py-2">{{ $buku->kategori->nama ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $buku->tahun_terbit }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <a href="{{ route('buku.show', $buku) }}" class="text-indigo-600 hover:underline">Detail</a>

                                            @can('ajukan-peminjaman-buku')
                                                <a href="{{ route('peminjaman.create', ['buku' => $buku->id]) }}" class="text-green-600 hover:underline">Ajukan Pinjam</a>
                                            @endcan

                                            @can('kelola-data-buku')
                                                <a href="{{ route('buku.edit', $buku) }}" class="text-yellow-600 hover:underline">Edit</a>
                                                <form method="POST" action="{{ route('buku.destroy', $buku) }}"
                                                      onsubmit="return confirm('Hapus buku ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data buku.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $bukus->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>