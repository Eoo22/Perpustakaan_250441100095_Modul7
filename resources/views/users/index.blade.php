<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Pengguna</h2>
            <a href="{{ route('users.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase text-white hover:bg-gray-700">Tambah Pengguna</a>
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
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Role</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $u)
                                <tr>
                                    <td class="px-4 py-2">{{ $u->name }}</td>
                                    <td class="px-4 py-2">{{ $u->email }}</td>
                                    <td class="px-4 py-2">{{ $u->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex gap-3">
                                            <a href="{{ route('users.edit', $u) }}" class="text-yellow-600 hover:underline">Edit</a>
                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.destroy', $u) }}"
                                                      onsubmit="return confirm('Hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>