<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * Display a listing of books with statistics and filtering.
     */
    public function index(Request $request)
    {
        $query = Buku::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('kategori_rak', 'like', "%{$search}%");
            });
        }

        // Category / Rak filter
        if ($request->filled('kategori')) {
            $query->where('kategori_rak', $request->kategori);
        }

        // Stock status filter
        if ($request->filled('status_stok')) {
            if ($request->status_stok === 'tersedia') {
                $query->where('stok_tersedia', '>', 2);
            } elseif ($request->status_stok === 'kritis') {
                $query->where('stok_tersedia', '>', 0)->where('stok_tersedia', '<=', 2);
            } elseif ($request->status_stok === 'habis') {
                $query->where('stok_tersedia', '<=', 0);
            }
        }

        // Sort by ID descending or user choice
        $buku = $query->orderBy('id_buku', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total_judul' => Buku::count(),
            'total_stok' => (int) Buku::sum('stok_tersedia'),
            'total_kategori' => Buku::whereNotNull('kategori_rak')->distinct('kategori_rak')->count('kategori_rak'),
            'stok_kritis' => Buku::where('stok_tersedia', '<=', 2)->count(),
        ];

        // Unique categories for filter dropdown
        $kategoriList = Buku::whereNotNull('kategori_rak')
            ->where('kategori_rak', '!=', '')
            ->distinct()
            ->orderBy('kategori_rak')
            ->pluck('kategori_rak');

        $rakList = Rak::orderBy('kode_rak')->get();

        return view('katalog-buku', compact('buku', 'stats', 'kategoriList', 'rakList'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'nullable|string|max:20',
            'judul' => 'required|string|max:200',
            'penulis' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:100',
            'kategori_rak' => 'nullable|string|max:50',
            'sinopsis' => 'nullable|string',
            'stok_tersedia' => 'required|integer|min:0',
        ]);

        $buku = Buku::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Buku baru berhasil ditambahkan ke katalog!',
                'data' => $buku
            ]);
        }

        return redirect()->back()->with('success', 'Buku baru "' . $buku->judul . '" berhasil ditambahkan ke katalog!');
    }

    /**
     * Display the specified book details (JSON for modal / view).
     */
    public function show($id)
    {
        $buku = Buku::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $buku
        ]);
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'isbn' => 'nullable|string|max:20',
            'judul' => 'required|string|max:200',
            'penulis' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:100',
            'kategori_rak' => 'nullable|string|max:50',
            'sinopsis' => 'nullable|string',
            'stok_tersedia' => 'required|integer|min:0',
        ]);

        $buku->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Informasi buku berhasil diperbarui!',
                'data' => $buku
            ]);
        }

        return redirect()->back()->with('success', 'Data buku "' . $buku->judul . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        // Check if book has loan references in peminjaman
        $isBorrowed = DB::table('peminjaman')->where('id_buku', $id)->exists();
        if ($isBorrowed) {
            $msg = 'Buku ini tidak dapat dihapus karena masih tercatat dalam riwayat transaksi peminjaman!';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $judul = $buku->judul;
        $buku->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Buku '{$judul}' berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->back()->with('success', "Buku '{$judul}' berhasil dihapus dari katalog!");
    }
}
