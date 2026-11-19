<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Sistem Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informasi Akun Aktif -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 gap-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Email: <span class="font-medium text-gray-800">{{ Auth::user()->email }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase bg-indigo-100 text-indigo-800">
                            Role Aktif: {{ Auth::user()->roles->pluck('name')->implode(', ') ?: 'User' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= PANEL ADMIN ================= -->
            @role('admin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 border-l-4 border-red-500">
                    <h4 class="font-bold text-red-700 text-lg mb-2">🛠️ Panel Kontrol Administrator</h4>
                    <p class="text-sm text-gray-600">Anda memiliki hak akses penuh untuk mengelola master data buku, konfigurasi role dan permission pengguna, serta memantau seluruh laporan sirkulasi perpustakaan.</p>
                </div>
            @endrole

            <!-- ================= PANEL PETUGAS ================= -->
            @role('petugas')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 border-l-4 border-yellow-500">
                    <h4 class="font-bold text-yellow-700 text-lg mb-2">📋 Panel Operasional Petugas</h4>
                    <p class="text-sm text-gray-600">Selamat bertugas! Anda bertanggung jawab mengelola inventaris buku serta memproses transaksi peminjaman dan pengembalian perpustakaan secara operasional.</p>
                </div>
            @endrole

            <!-- ================= PANEL SISWA ================= -->
            @role('siswa')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 border-l-4 border-green-500">
                    <h4 class="font-bold text-green-700 text-lg mb-2">📚 Area Layanan Siswa</h4>
                    <p class="text-sm text-gray-600">Selamat datang di portal perpustakaan. Anda dapat mencari referensi buku, melihat profil pribadi, serta memantau riwayat peminjaman buku Anda sendiri secara aman.</p>
                </div>
            @endrole

        </div>
    </div>
</x-app-layout>