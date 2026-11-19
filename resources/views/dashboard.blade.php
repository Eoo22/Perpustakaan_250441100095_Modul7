<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @php
                    $user = auth()->user();
                    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->implode(', ') : ($user?->role ?? '-');
                @endphp

                <p class="text-gray-900 font-medium">
                    Selamat datang, {{ $user?->name ?? 'Pengguna' }}!
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    Role aktif: <span class="font-semibold">{{ $roles ?: '-' }}</span>
                </p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('buku.index') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Lihat Daftar Buku
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>