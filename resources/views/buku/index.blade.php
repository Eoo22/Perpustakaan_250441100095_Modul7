<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Buku - Modul 6</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Formulir hanya tampil untuk Admin & Petugas -->
        @can('kelola-buku')
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 id="form-title" class="text-xl font-bold mb-4 text-gray-800">Tambah Buku Baru</h2>
            <form id="form-buku" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="buku_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Kategori</label>
                    <input type="number" id="kategori_id" name="kategori_id" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Buku</label>
                    <input type="text" id="judul" name="judul" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Penulis</label>
                    <input type="text" id="penulis" name="penulis" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="md:col-span-2 flex space-x-2">
                    <button type="submit" id="btn-submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">Simpan Buku</button>
                    <button type="button" id="btn-batal" onclick="resetForm()" class="hidden bg-gray-500 text-white font-semibold py-2 px-4 rounded-md">Batal</button>
                </div>
            </form>
        </div>
        @endcan

        <!-- Tabel Buku -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Daftar Buku Perpustakaan</h1>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border border-gray-300 p-2">Kategori</th>
                        <th class="border border-gray-300 p-2">Judul</th>
                        <th class="border border-gray-300 p-2">Penulis</th>
                        <th class="border border-gray-300 p-2">Tahun Terbit</th>
                        @can('kelola-buku')
                        <th class="border border-gray-300 p-2 text-center">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody id="tabel-buku">
                    <tr><td colspan="5" class="text-center p-4 text-gray-500">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const canKelola = @json(auth()->user()->can('kelola-buku'));

        async function ambilDataBuku() {
            try {
                const response = await fetch('/buku-cari?judul=');
                const result = await response.json();
                const dataBuku = Array.isArray(result) ? result : (result.data || []);
                const tbody = document.getElementById('tabel-buku');
                tbody.innerHTML = '';

                if (dataBuku.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${canKelola ? 5 : 4}" class="text-center p-4 text-gray-500">Belum ada data buku.</td></tr>`;
                    return;
                }

                dataBuku.forEach(buku => {
                    const tr = document.createElement('tr');
                    
                    // PERBAIKAN A1: Menggunakan escape text secara aman via DOM textContent (atau escape di level rendering)
                    // untuk mencegah eksekusi payload XSS (seperti tag <script>) pada sisi klien.
                    const tdKategori = document.createElement('td');
                    tdKategori.className = 'border border-gray-300 p-2';
                    tdKategori.textContent = buku.kategori_id || '-';

                    const tdJudul = document.createElement('td');
                    tdJudul.className = 'border border-gray-300 p-2';
                    tdJudul.textContent = buku.judul || '-'; // Mencegah XSS (menggantikan unescaped rendering)

                    const tdPenulis = document.createElement('td');
                    tdPenulis.className = 'border border-gray-300 p-2';
                    tdPenulis.textContent = buku.penulis || '-';

                    const tdTahun = document.createElement('td');
                    tdTahun.className = 'border border-gray-300 p-2';
                    tdTahun.textContent = buku.tahun_terbit || '-';

                    tr.appendChild(tdKategori);
                    tr.appendChild(tdJudul);
                    tr.appendChild(tdPenulis);
                    tr.appendChild(tdTahun);

                    if (canKelola) {
                        const tdAksi = document.createElement('td');
                        tdAksi.className = 'border border-gray-300 p-2 text-center space-x-2';
                        
                        const btnUbah = document.createElement('button');
                        btnUbah.className = 'bg-yellow-500 text-white px-3 py-1 rounded text-sm';
                        btnUbah.innerText = 'Ubah';
                        btnUbah.onclick = () => editBuku(buku);

                        const btnHapus = document.createElement('button');
                        btnHapus.className = 'bg-red-600 text-white px-3 py-1 rounded text-sm';
                        btnHapus.innerText = 'Hapus';
                        btnHapus.onclick = () => hapusBuku(buku.id);

                        tdAksi.appendChild(btnUbah);
                        tdAksi.appendChild(btnHapus);
                        tr.appendChild(tdAksi);
                    }

                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        const formBuku = document.getElementById('form-buku');
        if (formBuku) {
            formBuku.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = document.getElementById('buku_id').value;
                const formData = {
                    kategori_id: document.getElementById('kategori_id').value,
                    judul: document.getElementById('judul').value,
                    penulis: document.getElementById('penulis').value,
                    tahun_terbit: document.getElementById('tahun').value,
                };
                const url = id ? `/buku/${id}` : '/buku';
                const method = id ? 'PUT' : 'POST';
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify(formData)
                });
                if (response.ok) {
                    resetForm();
                    ambilDataBuku();
                    alert(id ? 'Buku berhasil diubah!' : 'Buku berhasil ditambahkan!');
                }
            });
        }

        function editBuku(buku) {
            document.getElementById('buku_id').value = buku.id;
            document.getElementById('kategori_id').value = buku.kategori_id || '';
            document.getElementById('judul').value = buku.judul || '';
            document.getElementById('penulis').value = buku.penulis || '';
            document.getElementById('tahun').value = buku.tahun_terbit || '';
            document.getElementById('form-title').innerText = 'Ubah Data Buku';
            document.getElementById('btn-submit').innerText = 'Perbarui Buku';
            document.getElementById('btn-batal').classList.remove('hidden');
        }

        function resetForm() {
            document.getElementById('form-buku').reset();
            document.getElementById('buku_id').value = '';
            document.getElementById('form-title').innerText = 'Tambah Buku Baru';
            document.getElementById('btn-submit').innerText = 'Simpan Buku';
            document.getElementById('btn-batal').classList.add('hidden');
        }

        async function hapusBuku(id) {
            if (!confirm('Yakin ingin menghapus buku ini?')) return;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(`/buku/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
            });
            if (response.ok) {
                ambilDataBuku();
                alert('Buku berhasil dihapus!');
            }
        }

        document.addEventListener('DOMContentLoaded', ambilDataBuku);
    </script>
</body>
</html>