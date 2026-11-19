<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="text-sm text-gray-500">Total Pengguna</div>
                <div class="text-3xl font-bold">{{ $totalPengguna }}</div>
            </div>
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="text-sm text-gray-500">Total Buku</div>
                <div class="text-3xl font-bold">{{ $totalBuku }}</div>
            </div>
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="text-sm text-gray-500">Total Peminjaman</div>
                <div class="text-3xl font-bold">{{ $totalPeminjaman }}</div>
            </div>
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="text-sm text-gray-500">Menunggu Persetujuan</div>
                <div class="text-3xl font-bold">{{ $menunggu }}</div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-red-500 text-sm text-gray-600">
                Anda dapat mengelola data buku, pengguna beserta role, peminjaman, pengembalian, dan laporan perpustakaan.
            </div>
        </div>
    </div>
</x-app-layout>