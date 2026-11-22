<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori; // Jangan lupa import model Kategori

class BukuController extends Controller
{
    // Halaman Daftar Buku (Menampilkan View atau JSON)
    public function index(Request $request)
    {
        // Jika permintaan berasal dari JavaScript (Fetch API / AJAX), kembalikan data JSON
        if ($request->wantsJson() || $request->ajax()) {
            $buku = Buku::with('kategori')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'penulis' => $item->penulis,
                    'tahun_terbit' => $item->tahun_terbit,
                    'kategori_id' => $item->kategori_id,
                    'kategori' => $item->kategori->nama ?? 'Umum',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $buku
            ]);
        }

        // Jika diakses langsung lewat browser, ambil datanya dan kirimkan ke view Blade
        $bukus = Buku::all();
        $kategoris = Kategori::all(); // Ambil semua data kategori
        // 3. Sertakan $kategoris ke dalam compact
        return view('buku.index', compact('bukus', 'kategoris'));
    }

    public function edit($id)
    {
        $bukus = Buku::all();
        $buku = Buku::findOrFail($id); // Mode edit dengan data buku yang dipilih
        $kategoris = Kategori::all();

        return view('buku.index', compact('buku', 'bukus', 'kategoris'));
    }

    // Menyimpan Buku Baru (POST)
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|min:3|max:150',
            'penulis' => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
        ], [
            'kategori_id.required' => 'Kategori wajib diisi.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid / tidak ada di database.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.min' => 'Judul buku minimal harus 3 karakter.',
            'judul.max' => 'Judul buku maksimal 150 karakter.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'penulis.min' => 'Nama penulis minimal harus 3 karakter.',
            'penulis.max' => 'Nama penulis maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa bilangan bulat.',
            'tahun_terbit.min' => 'Tahun terbit minimal adalah tahun 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
        ]);

        $buku = Buku::create([
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit,
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Buku berhasil ditambahkan',
        //     'data' => $buku
        // ]);

        // UBAH MENJADI REDIRECT KE HALAMAN INDEX DENGAN PESAN SUKSES:
        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Memperbarui Data Buku (PUT/PATCH)
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|min:3|max:150',
            'penulis' => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
        ], [
            'kategori_id.required' => 'Kategori wajib diisi.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid / tidak ada di database.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.min' => 'Judul buku minimal harus 3 karakter.',
            'judul.max' => 'Judul buku maksimal 150 karakter.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'penulis.min' => 'Nama penulis minimal harus 3 karakter.',
            'penulis.max' => 'Nama penulis maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa bilangan bulat.',
            'tahun_terbit.min' => 'Tahun terbit minimal adalah tahun 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update([
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit,
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Buku berhasil diperbarui',
        //     'data' => $buku
        // ]);

        // UBAH MENJADI REDIRECT KE HALAMAN UTAMA DENGAN PESAN SUKSES:
        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    // Menghapus Buku (DELETE)
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Buku berhasil dihapus'
        // ]);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil dihapus!');
    }

    public function katalogSiswa()
    {
        $buku = Buku::all();
        return view('siswa.buku', compact('buku'));
    }

    // b
    public function cari(Request $request)
    {
        $judul = $request->query('judul');
        $buku = DB::select("SELECT * FROM bukus WHERE judul = ?", [$judul]);
        return response()->json($buku);
    }
}