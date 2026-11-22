<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight bg-blue-600 -m-6 p-6">
            {{ __('Kelola Data Buku') }}
        </h2>
    </x-slot>

    <!-- Memuat CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h2 class="mb-4">Data Buku Perpustakaan Cendekia</h2>

                    <!-- Notifikasi Sukses / Pesan -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Card Form Tambah / Edit Buku -->
                    <div class="card mb-4 p-3 shadow-sm">
                        <!-- Judul form berubah dinamis -->
                        <h4>{{ isset($buku) ? 'Edit Buku: ' . $buku->judul : 'Tambah Buku Baru' }}</h4>

                        @can('kelola-buku')
                            <!-- Form mengarah ke update jika mode edit, atau store jika tambah baru -->
                            <form action="{{ isset($buku) ? route('buku.update', $buku->id) : route('buku.store') }}"
                                method="POST">
                                @csrf

                                <!-- Tambahkan method PUT jika sedang dalam mode edit -->
                                @if(isset($buku))
                                    @method('PUT')
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Kategori Buku</label>
                                    <select name="kategori_id"
                                        class="form-select @error('kategori_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $buku->kategori_id ?? '') == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama }}
                                                <!-- Sesuaikan dengan nama kolom nama kategori di database Anda (misal: nama atau nama_kategori) -->
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Judul Buku</label>
                                    <input type="text" name="judul"
                                        class="form-control @error('judul') is-invalid @enderror"
                                        value="{{ old('judul', $buku->judul ?? '') }}" required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Penulis</label>
                                    <input type="text" name="penulis"
                                        class="form-control @error('penulis') is-invalid @enderror"
                                        value="{{ old('penulis', $buku->penulis ?? '') }}" required>
                                    @error('penulis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit"
                                        class="form-control @error('tahun_terbit') is-invalid @enderror"
                                        value="{{ old('tahun_terbit', $buku->tahun_terbit ?? '') }}" required>
                                    @error('tahun_terbit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tombol submit dinamis -->
                                <button type="submit" class="btn {{ isset($buku) ? 'btn-success' : 'btn-primary' }}">
                                    {{ isset($buku) ? 'Perbarui Data' : 'Simpan' }}
                                </button>

                                <!-- Tombol Batal untuk kembali ke mode tambah -->
                                @if(isset($buku))
                                    <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
                                @endif
                            </form>
                        @endcan
                    </div>

                    <!-- Tabel Data Buku (Dirender langsung via Blade) -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Tahun Terbit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bukus as $buku)
                                    <tr>
                                        <td>{{ $buku->kategori->nama ?? '-' }}</td>

                                        <td>{{ $buku->judul }}</td>
                                        <td>{{ $buku->penulis }}</td>
                                        <td>{{ $buku->tahun_terbit }}</td>
                                        <td>
                                            @can('kelola-buku')
                                                <!-- Tombol Edit yang menghubungkan ke halaman edit -->
                                                <a href="{{ route('buku.edit', $buku->id) }}"
                                                    class="btn btn-warning btn-sm me-1">Edit</a>
                                                <!-- Tombol Hapus (Menyesuaikan Soal A3/Soal 2c nantinya) -->
                                                <form action="{{ route('buku.destroy', $buku->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>