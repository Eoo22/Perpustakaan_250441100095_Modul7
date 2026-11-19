<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Perpustakaan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="mb-3 font-semibold">Peminjaman per Status</h3>
                <div class="flex flex-wrap gap-4 text-sm">
                    @foreach (['menunggu', 'dipinjam', 'dikembalikan', 'ditolak'] as $s)
                        <div class="rounded-md bg-gray-50 px-4 py-3">
                            <div class="text-gray-500">{{ ucfirst($s) }}</div>
                            <div class="text-2xl font-bold">{{ $perStatus[$s] ?? 0 }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="mb-3 font-semibold">Jumlah Buku per Kategori</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr><th class="px-4 py-2">Kategori</th><th class="px-4 py-2">Total Buku</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($bukuPerKategori as $row)
                            <tr><td class="px-4 py-2">{{ $row->kategori }}</td><td class="px-4 py-2">{{ $row->total_buku }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="mb-3 font-semibold">Total Peminjaman per Buku</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr><th class="px-4 py-2">Judul</th><th class="px-4 py-2">Total Peminjaman</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($peminjamanPerBuku as $buku)
                            <tr><td class="px-4 py-2">{{ $buku->judul }}</td><td class="px-4 py-2">{{ $buku->peminjaman_count }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>