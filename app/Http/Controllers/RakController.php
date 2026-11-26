<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    /**
     * Modul CRUD Rak Buku Perpustakaan Kampus
     */

    // Menampilkan daftar rak buku beserta kapasitas & pencarian (Read)
    public function index(Request $request)
    {
        $query = Rak::query();

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_rak', 'like', "%{$search}%")
                  ->orWhere('nama_rak', 'like', "%{$search}%")
                  ->orWhere('kategori_buku', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori buku
        if ($request->filled('kategori')) {
            $query->where('kategori_buku', $request->kategori);
        }

        $rakList = $query->orderBy('id_rak', 'asc')->get();

        // Hitung statistik metric card (tanpa lantai)
        $stats = [
            'total_rak' => Rak::count(),
            'total_kapasitas' => (int) Rak::sum('kapasitas_maksimal'),
            'total_kategori' => Rak::distinct('kategori_buku')->count('kategori_buku'),
            'total_buku' => Buku::count(),
        ];

        // Daftar pilihan kategori untuk dropdown filter
        $kategoriList = Rak::distinct()->pluck('kategori_buku')->filter()->values();

        return view('rak.index', compact('rakList', 'stats', 'kategoriList'));
    }

    // Menambahkan rak buku baru ke database (Insert)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_rak' => 'required|string|max:100',
            'kategori_buku' => 'required|string|max:100',
            'kapasitas_maksimal' => 'required|integer|min:1',
            'kode_rak' => 'nullable|string|max:30|unique:rak_buku,kode_rak',
            'keterangan' => 'nullable|string',
        ], [
            'nama_rak.required' => 'Nama rak wajib diisi.',
            'kategori_buku.required' => 'Kategori buku wajib diisi.',
            'kapasitas_maksimal.required' => 'Kapasitas maksimal wajib diisi.',
            'kode_rak.unique' => 'Kode rak ini sudah digunakan.',
        ]);

        // Buat kode rak otomatis jika tidak diisi manual (contoh: RAK-001)
        if (empty($validated['kode_rak'])) {
            $count = Rak::count() + 1;
            $validated['kode_rak'] = 'RAK-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $rak = Rak::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Rak buku baru berhasil ditambahkan!',
                'data' => $rak,
            ]);
        }

        return redirect()->route('rak.index')->with('success', "Rak buku '{$rak->nama_rak}' ({$rak->kode_rak}) berhasil ditambahkan ke sistem!");
    }

    // Mengambil rincian data 1 rak (Read Detail)
    public function show($id)
    {
        $rak = Rak::findOrFail($id);

        // Ambil daftar buku yang tersimpan di rak ini
        $bukuDiRak = Buku::where('kategori_rak', 'like', "%{$rak->kode_rak}%")
            ->orWhere('kategori_rak', 'like', "%{$rak->nama_rak}%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rak,
            'buku_list' => $bukuDiRak,
            'total_buku' => count($bukuDiRak),
        ]);
    }

    // Mengedit informasi data rak buku (Update)
    public function update(Request $request, $id)
    {
        $rak = Rak::findOrFail($id);

        $validated = $request->validate([
            'nama_rak' => 'required|string|max:100',
            'kategori_buku' => 'required|string|max:100',
            'kapasitas_maksimal' => 'required|integer|min:1',
            'kode_rak' => 'required|string|max:30|unique:rak_buku,kode_rak,' . $id . ',id_rak',
            'keterangan' => 'nullable|string',
        ], [
            'nama_rak.required' => 'Nama rak wajib diisi.',
            'kode_rak.required' => 'Kode rak wajib diisi.',
            'kode_rak.unique' => 'Kode rak ini sudah digunakan oleh rak lain.',
        ]);

        $rak->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Informasi rak buku berhasil diperbarui!',
                'data' => $rak,
            ]);
        }

        return redirect()->route('rak.index')->with('success', "Data rak '{$rak->nama_rak}' berhasil diperbarui!");
    }

    // Menghapus data rak buku dari sistem (Delete)
    public function destroy(Request $request, $id)
    {
        $rak = Rak::findOrFail($id);
        $nama = $rak->nama_rak;
        $kode = $rak->kode_rak;

        // Cek apakah ada buku di katalog yang tersimpan di rak ini
        $adaBuku = Buku::where('kategori_rak', 'like', "%{$kode}%")
            ->orWhere('kategori_rak', 'like', "%{$nama}%")
            ->exists();

        if ($adaBuku) {
            $msg = "Rak '{$nama}' tidak dapat dihapus karena masih tercatat menampung koleksi buku di katalog!";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $rak->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Rak '{$nama}' berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('rak.index')->with('success', "Data rak '{$nama}' ({$kode}) berhasil dihapus!");
    }
}
