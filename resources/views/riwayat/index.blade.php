<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Peminjaman Saya</h2>
            @can('ajukan-peminjaman-buku')
                <a href="{{ route('peminjaman.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white hover:bg-gray-700">Ajukan Peminjaman</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-2">Buku</th>
                                <th class="px-4 py-2">Tgl Pinjam</th>
                                <th class="px-4 py-2">Tgl Kembali</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($peminjamans as $p)
                                <tr>
                                    <td class="px-4 py-2">{{ $p->buku->judul ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $p->tanggal_pinjam }}</td>
                                    <td class="px-4 py-2">{{ $p->tanggal_kembali ?? '-' }}</td>
                                    <td class="px-4 py-2"><x-status-badge :status="$p->status" /></td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('riwayat.show', $p) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat peminjaman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $peminjamans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>