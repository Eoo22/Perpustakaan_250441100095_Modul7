<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Peminjaman</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('peminjaman.index') }}" class="mb-4 flex gap-2">
                    <select name="status" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua status</option>
                        @foreach (['menunggu', 'dipinjam', 'dikembalikan', 'ditolak'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white">Filter</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-2">Anggota</th>
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
                                    <td class="px-4 py-2">{{ $p->anggota->nama ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $p->buku->judul ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $p->tanggal_pinjam }}</td>
                                    <td class="px-4 py-2">{{ $p->tanggal_kembali ?? '-' }}</td>
                                    <td class="px-4 py-2"><x-status-badge :status="$p->status" /></td>
                                    <td class="px-4 py-2">
                                        @can('proses-persetujuan-peminjaman')
                                            @if ($p->status === 'menunggu')
                                                <div class="flex gap-3">
                                                    <form method="POST" action="{{ route('peminjaman.setujui', $p) }}">
                                                        @csrf
                                                        <button class="text-green-600 hover:underline">Setujui</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('peminjaman.tolak', $p) }}">
                                                        @csrf
                                                        <button class="text-red-600 hover:underline">Tolak</button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data peminjaman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $peminjamans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>