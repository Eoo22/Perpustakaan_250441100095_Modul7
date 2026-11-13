<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Buku - Tugas Praktikum Modul 6</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- SOAL 3: FORMULIR TAMBAH / UBAH BUKU (Hanya tampil untuk Admin & Petugas via @can) -->
        @can('kelola-buku')
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 id="form-title" class="text-xl font-bold mb-4 text-gray-800">Tambah Buku Baru</h2>
            <form id="form-buku" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="buku_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Kategori (Angka)</label>
                    <input type="number" id="kategori_id" name="kategori_id" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Buku</label>
                    <input type="text" id="judul" name="judul" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Penulis</label>
                    <input type="text" id="penulis" name="penulis" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm">
                </div>
                <div class="md:col-span-2 flex space-x-2">
                    <button type="submit" id="btn-submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-700 transition">
                        Simpan Buku
                    </button>
                    <button type="button" id="btn-batal" onclick="resetForm()" class="hidden bg-gray-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-gray-600 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
        @endcan

        <!-- TABEL DAFTAR BUKU -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Daftar Buku Perpustakaan</h1>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border border-gray-300 p-2">Kategori</th>
                        <th class="border border-gray-300 p-2">Judul</th>
                        <th class="border border-gray-300 p-2">Penulis</th>
                        <th class="border border-gray-300 p-2">Tahun Terbit</th>
                        <!-- SOAL 3: Kolom Aksi hanya tampil jika punya izin kelola-buku -->
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

    <!-- SCRIPT JAVASCRIPT CRUD -->
    <script>
        const canKelola = @json(auth()->user()->can('kelola-buku'));

        async function ambilDataBuku() {
            try {
                const response = await fetch('/buku-cari?judul=');
                if (!response.ok) throw new Error('Gagal mengambil data dari server.');
                
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
                    
                    let aksiHtml = '';
                    if (canKelola) {
                        aksiHtml = `
                            <td class="border border-gray-300 p-2 text-center space-x-2">
                                <button onclick='editBuku(${JSON.stringify(buku)})' class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Ubah</button>
                                <button onclick="hapusBuku(${buku.id})" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">Hapus</button>
                            </td>
                        `;
                    }

                    tr.innerHTML = `
                        <td class="border border-gray-300 p-2">${buku.kategori_id || buku.kategori || '-'}</td>
                        <td class="border border-gray-300 p-2">${buku.judul || buku.judul_buku || '-'}</td>
                        <td class="border border-gray-300 p-2">${buku.penulis || '-'}</td>
                        <td class="border border-gray-300 p-2">${buku.tahun_terbit || buku.tahun || '-'}</td>
                        ${aksiHtml}
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tabel-buku').innerHTML = `<tr><td colspan="5" class="text-center p-4 text-red-500">Gagal memuat data.</td></tr>`;
            }
        }

        // Handle Submit (Tambah / Ubah)
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

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(formData)
                    });

                    if (response.ok) {
                        resetForm();
                        ambilDataBuku();
                        alert(id ? 'Data buku berhasil diubah!' : 'Data buku berhasil ditambahkan!');
                    } else {
                        const errData = await response.json();
                        alert('Gagal menyimpan: ' + JSON.stringify(errData.errors || errData.message));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi.');
                }
            });
        }

        function editBuku(buku) {
            document.getElementById('buku_id').value = buku.id;
            document.getElementById('kategori_id').value = buku.kategori_id || buku.kategori || '';
            document.getElementById('judul').value = buku.judul || buku.judul_buku || '';
            document.getElementById('penulis').value = buku.penulis || '';
            document.getElementById('tahun').value = buku.tahun_terbit || buku.tahun || '';
            
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
            if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/buku/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                if (response.ok) {
                    ambilDataBuku();
                    alert('Buku berhasil dihapus!');
                } else {
                    const errData = await response.json();
                    alert('Gagal menghapus buku: ' + (errData.message || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            }
        }

        document.addEventListener('DOMContentLoaded', ambilDataBuku);
    </script>
</body>
</html>