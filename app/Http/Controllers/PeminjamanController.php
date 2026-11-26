<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PengembalianDenda;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * Modul Transaksi Peminjaman Buku
     * Sinkron dengan Modul Pengembalian Buku & Denda
     */

    // Menampilkan daftar peminjaman buku (Read)
    public function index(Request $request)
    {
        // Eager load anggota, buku, dan data pengembalian/denda
        $query = Peminjaman::with(['anggota', 'buku', 'pengembalian']);

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function ($qa) use ($search) {
                      $qa->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nim_nidn', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function ($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%")
                         ->orWhere('isbn', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status transaksi (Dipinjam / Selesai / Terlambat)
        if ($request->filled('status')) {
            if ($request->status === 'Dipinjam') {
                $query->where('status_peminjaman', 'Dipinjam');
            } elseif ($request->status === 'Selesai') {
                $query->whereIn('status_peminjaman', ['Selesai', 'Sudah Dikembalikan']);
            } elseif ($request->status === 'Terlambat') {
                $query->where('status_peminjaman', 'Dipinjam')
                      ->where('tanggal_jatuh_tempo', '<', date('Y-m-d'));
            }
        }

        $peminjaman = $query->orderBy('id_peminjaman', 'desc')->get();

        // Hitung statistik untuk metric card
        $today = date('Y-m-d');
        $stats = [
            'total_transaksi' => Peminjaman::count(),
            'sedang_dipinjam' => Peminjaman::where('status_peminjaman', 'Dipinjam')->count(),
            'sudah_selesai' => Peminjaman::whereIn('status_peminjaman', ['Selesai', 'Sudah Dikembalikan'])->count(),
            'terlambat' => Peminjaman::where('status_peminjaman', 'Dipinjam')
                                     ->where('tanggal_jatuh_tempo', '<', $today)
                                     ->count(),
        ];

        // Ambil data anggota aktif dan buku untuk form insert & edit
        $anggotaList = Anggota::where('status_aktif', 'Aktif')->orderBy('nama_lengkap')->get();
        $bukuList = Buku::where('stok_tersedia', '>', 0)->orderBy('judul')->get();
        $allBukuList = Buku::orderBy('judul')->get(); // Untuk dropdown modal edit (agar buku terpilih tetap muncul)

        return view('peminjaman.index', compact('peminjaman', 'stats', 'anggotaList', 'bukuList', 'allBukuList'));
    }

    // Menambahkan transaksi peminjaman baru (Insert)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'id_buku' => 'required|exists:buku,id_buku',
            'tanggal_pinjam' => 'required|date',
            'durasi_hari' => 'nullable|integer|min:1|max:30',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $buku = Buku::findOrFail($validated['id_buku']);

        // Cek ketersediaan stok fisik buku
        if ($buku->stok_tersedia <= 0) {
            return redirect()->back()->with('error', 'Stok buku "' . $buku->judul . '" sedang habis! Tidak dapat meminjam.');
        }

        // Tentukan tanggal jatuh tempo
        $tanggalPinjam = $validated['tanggal_pinjam'];
        if (!empty($validated['tanggal_jatuh_tempo'])) {
            $tanggalJatuhTempo = $validated['tanggal_jatuh_tempo'];
        } else {
            $durasi = $request->durasi_hari ?? 7;
            $tanggalJatuhTempo = date('Y-m-d', strtotime($tanggalPinjam . " +{$durasi} days"));
        }

        // Generate kode transaksi peminjaman otomatis
        $kodeTransaksi = 'TRX-PJ-' . date('Ymd') . '-' . str_pad((Peminjaman::count() + 1), 3, '0', STR_PAD_LEFT);

        // Simpan data transaksi peminjaman
        Peminjaman::create([
            'kode_transaksi' => $kodeTransaksi,
            'id_anggota' => $validated['id_anggota'],
            'id_buku' => $validated['id_buku'],
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'status_peminjaman' => 'Dipinjam',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Kurangi stok buku yang tersedia sebanyak 1
        $buku->decrement('stok_tersedia', 1);

        return redirect()->route('peminjaman.index')->with('success', 'Transaksi peminjaman (' . $kodeTransaksi . ') berhasil dicatat! Data langsung tersinkron ke Modul Pengembalian & Denda.');
    }

    // Mengambil detail transaksi peminjaman
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku', 'pengembalian'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $peminjaman
        ]);
    }

    // Mengedit transaksi peminjaman (Update Informasi: Peminjam, Buku, Waktu Pinjam, Jatuh Tempo, Catatan)
    // Sesuai aturan: Approve pengembalian & denda TIDAK BISA dilakukan di sini (hanya di modul pengembalian)
    public function update(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'id_buku' => 'required|exists:buku,id_buku',
            'tanggal_pinjam' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'catatan' => 'nullable|string',
        ]);

        // Jika buku yang dipinjam diganti
        if ($peminjaman->id_buku != $validated['id_buku']) {
            $bukuBaru = Buku::findOrFail($validated['id_buku']);

            // Jika masih berstatus dipinjam, cek ketersediaan stok buku baru
            if ($peminjaman->status_peminjaman === 'Dipinjam') {
                if ($bukuBaru->stok_tersedia <= 0) {
                    return redirect()->back()->with('error', 'Stok buku baru "' . $bukuBaru->judul . '" sedang kosong!');
                }
                // Kembalikan stok buku lama (+1) dan kurangi stok buku baru (-1)
                Buku::where('id_buku', $peminjaman->id_buku)->increment('stok_tersedia', 1);
                $bukuBaru->decrement('stok_tersedia', 1);
            }
        }

        // Update data peminjaman (TANPA mengubah status pengembalian & denda)
        $peminjaman->update([
            'id_anggota' => $validated['id_anggota'],
            'id_buku' => $validated['id_buku'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Transaksi peminjaman (' . $peminjaman->kode_transaksi . ') berhasil diperbarui! Perubahan data otomatis tersinkron ke Modul Pengembalian.');
    }

    // Melakukan perpanjangan durasi pinjam / update tanggal jatuh tempo (Shortcut Update)
    public function perpanjang(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status_peminjaman !== 'Dipinjam') {
            return redirect()->back()->with('error', 'Hanya peminjaman berstatus "Dipinjam" yang dapat diperpanjang!');
        }

        $request->validate([
            'tambah_hari' => 'nullable|integer|min:1|max:14',
            'tanggal_baru' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        if ($request->filled('tanggal_baru')) {
            $peminjaman->tanggal_jatuh_tempo = $request->tanggal_baru;
        } else {
            $tambah = $request->tambah_hari ?? 7;
            $peminjaman->tanggal_jatuh_tempo = date('Y-m-d', strtotime($peminjaman->tanggal_jatuh_tempo . " +{$tambah} days"));
        }

        if ($request->filled('catatan')) {
            $peminjaman->catatan = ($peminjaman->catatan ? $peminjaman->catatan . " | " : "") . "[Perpanjangan]: " . $request->catatan;
        }

        $peminjaman->save();

        return redirect()->route('peminjaman.index')->with('success', 'Durasi peminjaman (' . $peminjaman->kode_transaksi . ') berhasil diperpanjang hingga ' . date('d M Y', strtotime($peminjaman->tanggal_jatuh_tempo)) . '!');
    }

    // Membatalkan atau menghapus transaksi peminjaman jika salah input (Delete)
    public function destroy(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $kode = $peminjaman->kode_transaksi;

        // Jika transaksi masih berstatus "Dipinjam", kembalikan stok buku
        if ($peminjaman->status_peminjaman === 'Dipinjam') {
            Buku::where('id_buku', $peminjaman->id_buku)->increment('stok_tersedia', 1);
        }

        // Hapus juga catatan denda pengembalian jika ada, agar data kedua modul sinkron
        PengembalianDenda::where('id_peminjaman', $peminjaman->id_peminjaman)->delete();

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', "Transaksi peminjaman '{$kode}' berhasil dihapus dari kedua modul!");
    }
}
