<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Buku
            </h2>

            @can('kelola-buku')
                <a href="{{ route('buku.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                    Tambah Buku
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('buku.index') }}" class="mb-4 flex gap-2">
                    <input type="text" name="q" value="{{ $keyword ?? '' }}" placeholder="Cari judul buku..."
                           class="border-gray-300 rounded-md shadow-sm text-sm w-64">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">
                        Cari
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50 text-left">
                                <th class="px-4 py-2">Judul</th>
                                <th class="px-4 py-2">Penulis</th>
                                <th class="px-4 py-2">Stok</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bukus as $buku)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $buku->judul }}</td>
                                    <td class="px-4 py-2">{{ $buku->penulis ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $buku->stok ?? '-' }}</td>
                                    <td class="px-4 py-2 flex items-center gap-3">
                                        <a href="{{ route('buku.show', $buku) }}" class="text-blue-600 hover:underline">
                                            Detail
                                        </a>

                                        @can('kelola-buku')
                                            <a href="{{ route('buku.edit', $buku) }}" class="text-amber-600 hover:underline">
                                                Ubah
                                            </a>

                                            <form action="{{ route('buku.destroy', $buku) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus buku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                        Data buku belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>