<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pengadaan;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    /**
     * Modul 5: Manajemen Pengadaan Buku Baru & Data Supplier
     * Kontributor: Rowin Journandez
     */

    // Menampilkan riwayat transaksi pengadaan dan data supplier (Read)
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pengadaan');

        // Query Pengadaan Buku
        $queryPengadaan = Pengadaan::with(['supplier', 'buku']);
        if ($request->filled('search') && $tab === 'pengadaan') {
            $search = $request->search;
            $queryPengadaan->where(function ($q) use ($search) {
                $q->where('kode_pengadaan', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($qs) use ($search) {
                      $qs->where('nama_supplier', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function ($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%")
                         ->orWhere('isbn', 'like', "%{$search}%");
                  });
            });
        }
        $pengadaan = $queryPengadaan->orderBy('id_pengadaan', 'desc')->get();

        // Query Data Supplier
        $querySupplier = Supplier::query();
        if ($request->filled('search') && $tab === 'supplier') {
            $search = $request->search;
            $querySupplier->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%")
                  ->orWhere('kontak_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $suppliers = $querySupplier->orderBy('id_supplier', 'desc')->get();

        // Hitung statistik metric cards
        $stats = [
            'total_pengadaan' => Pengadaan::count(),
            'total_buku_masuk' => (int) Pengadaan::sum('jumlah_masuk'),
            'total_supplier' => Supplier::count(),
            'total_anggaran' => (float) Pengadaan::sum('total_biaya'),
        ];

        // Daftar buku untuk pilihan select form pengadaan
        $bukuList = Buku::orderBy('judul')->get();

        return view('pengadaan.index', compact('pengadaan', 'suppliers', 'stats', 'bukuList', 'tab'));
    }

    // Mencatat transaksi pengadaan stok buku baru (Insert)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_buku' => 'required|exists:buku,id_buku',
            'tanggal_pengadaan' => 'required|date',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'sumber_dana' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $totalBiaya = $validated['jumlah_masuk'] * $validated['harga_satuan'];
        $kodePengadaan = 'PO-' . date('Ymd') . '-' . str_pad((Pengadaan::count() + 1), 3, '0', STR_PAD_LEFT);

        $pengadaan = Pengadaan::create([
            'kode_pengadaan' => $kodePengadaan,
            'id_supplier' => $validated['id_supplier'],
            'id_buku' => $validated['id_buku'],
            'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
            'jumlah_masuk' => $validated['jumlah_masuk'],
            'harga_satuan' => $validated['harga_satuan'],
            'total_biaya' => $totalBiaya,
            'sumber_dana' => $validated['sumber_dana'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Tambahkan stok buku yang diterima ke katalog buku
        Buku::where('id_buku', $validated['id_buku'])->increment('stok_tersedia', $validated['jumlah_masuk']);

        return redirect()->route('pengadaan.index', ['tab' => 'pengadaan'])
            ->with('success', 'Transaksi pengadaan (' . $kodePengadaan . ') berhasil dicatat! Stok buku otomatis bertambah +' . $validated['jumlah_masuk'] . '.');
    }

    // Merevisi jumlah stok atau data transaksi pengadaan buku (Update)
    public function update(Request $request, $id)
    {
        $pengadaan = Pengadaan::findOrFail($id);

        $validated = $request->validate([
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'tanggal_pengadaan' => 'required|date',
            'jumlah_masuk' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'sumber_dana' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Hitung selisih perubahan stok buku
        $selisihStok = $validated['jumlah_masuk'] - $pengadaan->jumlah_masuk;
        $totalBiaya = $validated['jumlah_masuk'] * $validated['harga_satuan'];

        $pengadaan->update([
            'id_supplier' => $validated['id_supplier'],
            'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
            'jumlah_masuk' => $validated['jumlah_masuk'],
            'harga_satuan' => $validated['harga_satuan'],
            'total_biaya' => $totalBiaya,
            'sumber_dana' => $validated['sumber_dana'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Sesuaikan stok buku di katalog berdasarkan selisih revisi
        if ($selisihStok != 0) {
            Buku::where('id_buku', $pengadaan->id_buku)->increment('stok_tersedia', $selisihStok);
        }

        return redirect()->route('pengadaan.index', ['tab' => 'pengadaan'])
            ->with('success', 'Data pengadaan ' . $pengadaan->kode_pengadaan . ' berhasil direvisi!');
    }

    // Membatalkan input pendaftaran pengadaan yang salah (Delete)
    public function destroy(Request $request, $id)
    {
        $pengadaan = Pengadaan::findOrFail($id);
        $kode = $pengadaan->kode_pengadaan;
        $jumlahMasuk = $pengadaan->jumlah_masuk;
        $idBuku = $pengadaan->id_buku;

        // Kurangi kembali stok buku yang sebelumnya ditambahkan
        Buku::where('id_buku', $idBuku)->decrement('stok_tersedia', $jumlahMasuk);

        $pengadaan->delete();

        return redirect()->route('pengadaan.index', ['tab' => 'pengadaan'])
            ->with('success', "Transaksi pengadaan '{$kode}' berhasil dibatalkan dan stok dikurangi sebanyak {$jumlahMasuk}!");
    }

    // ==========================================================
    // AKSI CRUD SUPPLIER
    // ==========================================================

    // Menyimpan data supplier baru (Insert Supplier)
    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $kodeSupplier = 'SUP-' . str_pad((Supplier::count() + 1), 3, '0', STR_PAD_LEFT);
        $validated['kode_supplier'] = $request->filled('kode_supplier') ? $request->kode_supplier : $kodeSupplier;

        $supplier = Supplier::create($validated);

        return redirect()->route('pengadaan.index', ['tab' => 'supplier'])
            ->with('success', 'Supplier baru "' . $supplier->nama_supplier . '" berhasil didaftarkan!');
    }

    // Mengedit data supplier (Update Supplier)
    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('pengadaan.index', ['tab' => 'supplier'])
            ->with('success', 'Data supplier "' . $supplier->nama_supplier . '" berhasil diperbarui!');
    }

    // Menghapus data supplier (Delete Supplier)
    public function destroySupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        // Cek apakah supplier ini memiliki riwayat pengadaan buku
        $adaPengadaan = Pengadaan::where('id_supplier', $id)->exists();
        if ($adaPengadaan) {
            return redirect()->route('pengadaan.index', ['tab' => 'supplier'])
                ->with('error', 'Supplier "' . $supplier->nama_supplier . '" tidak dapat dihapus karena memiliki riwayat transaksi pengadaan buku!');
        }

        $nama = $supplier->nama_supplier;
        $supplier->delete();

        return redirect()->route('pengadaan.index', ['tab' => 'supplier'])
            ->with('success', "Data supplier '{$nama}' berhasil dihapus!");
    }
}
