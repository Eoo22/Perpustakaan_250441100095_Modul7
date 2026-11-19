<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan Peminjaman Buku</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('peminjaman.store') }}">
                    @csrf

                    <x-input-label for="buku_id" value="Pilih Buku" />
                    <select id="buku_id" name="buku_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Pilih buku --</option>
                        @foreach ($bukus as $buku)
                            <option value="{{ $buku->id }}" @selected(old('buku_id', request('buku')) == $buku->id)>
                                {{ $buku->judul }} - {{ $buku->penulis }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('buku_id')" class="mt-2" />

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Ajukan</x-primary-button>
                        <a href="{{ route('buku.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>