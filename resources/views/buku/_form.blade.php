@csrf

<div>
    <x-input-label for="kategori_id" value="Kategori" />
    <select id="kategori_id" name="kategori_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">-- Pilih kategori --</option>
        @foreach ($kategoris as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $buku->kategori_id ?? '') == $kategori->id)>
                {{ $kategori->nama }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="judul" value="Judul" />
    <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul', $buku->judul ?? '')" />
    <x-input-error :messages="$errors->get('judul')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="penulis" value="Penulis" />
    <x-text-input id="penulis" name="penulis" type="text" class="mt-1 block w-full" :value="old('penulis', $buku->penulis ?? '')" />
    <x-input-error :messages="$errors->get('penulis')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="tahun_terbit" value="Tahun Terbit" />
    <x-text-input id="tahun_terbit" name="tahun_terbit" type="number" class="mt-1 block w-full" :value="old('tahun_terbit', $buku->tahun_terbit ?? '')" />
    <x-input-error :messages="$errors->get('tahun_terbit')" class="mt-2" />
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('buku.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
</div>