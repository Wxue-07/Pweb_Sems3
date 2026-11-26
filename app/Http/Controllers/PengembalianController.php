<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\PengembalianDenda;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    /**
     * Modul Transaksi Pengembalian dan Denda
     * Sinkron penuh dengan Modul Transaksi Peminjaman Buku
     */

    // Menampilkan daftar seluruh transaksi peminjaman & status pengembalian / denda (Read)
    public function index(Request $request)
    {
        // Query seluruh transaksi peminjaman beserta relasi anggota, buku, dan catatan pengembalian
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
                  })
                  ->orWhereHas('pengembalian', function ($qp) use ($search) {
                      $qp->where('kode_pengembalian', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status Pengembalian / Pembayaran Denda
        if ($request->filled('status')) {
            if ($request->status === 'Dipinjam') {
                $query->where('status_peminjaman', 'Dipinjam');
            } elseif ($request->status === 'Dikembalikan') {
                $query->whereIn('status_peminjaman', ['Selesai', 'Sudah Dikembalikan']);
            } elseif ($request->status === 'Belum_Lunas') {
                $query->whereHas('pengembalian', function ($qp) {
                    $qp->where('status_pembayaran', 'Belum Lunas');
                });
            } elseif ($request->status === 'Lunas') {
                $query->whereHas('pengembalian', function ($qp) {
                    $qp->where('status_pembayaran', 'Lunas');
                });
            } elseif ($request->status === 'Bebas_Denda') {
                $query->whereHas('pengembalian', function ($qp) {
                    $qp->where('status_pembayaran', 'Bebas Denda');
                });
            }
        }

        // Urutkan transaksi terbaru di atas
        $transaksiList = $query->orderBy('id_peminjaman', 'desc')->get();

        // Hitung statistik untuk metric card
        $stats = [
            'total_transaksi' => Peminjaman::count(),
            'menunggu_kembali' => Peminjaman::where('status_peminjaman', 'Dipinjam')->count(),
            'sudah_kembali' => Peminjaman::whereIn('status_peminjaman', ['Selesai', 'Sudah Dikembalikan'])->count(),
            'denda_tertunggak' => (float) PengembalianDenda::where('status_pembayaran', 'Belum Lunas')->sum('total_denda'),
            'denda_lunas' => (float) PengembalianDenda::where('status_pembayaran', 'Lunas')->sum('total_denda'),
        ];

        // Daftar peminjaman yang masih aktif untuk dropdown modal approve pengembalian
        $peminjamanAktif = Peminjaman::with(['anggota', 'buku'])
            ->where('status_peminjaman', 'Dipinjam')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        return view('pengembalian.index', compact('transaksiList', 'stats', 'peminjamanAktif'));
    }

    // Memproses & Approve Pengembalian Buku serta Kalkulasi Denda (Insert/Approve)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_peminjaman' => 'required|exists:peminjaman,id_peminjaman',
            'tanggal_dikembalikan' => 'required|date',
            'kondisi_buku' => 'required|string',
            'status_bayar_langsung' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($validated['id_peminjaman']);

        // Cegah approve ganda
        if (in_array($peminjaman->status_peminjaman, ['Selesai', 'Sudah Dikembalikan'])) {
            return redirect()->back()->with('error', 'Transaksi ' . $peminjaman->kode_transaksi . ' sudah pernah disetujui pengembaliannya!');
        }

        // 1. Hitung keterlambatan hari
        $tglJatuhTempo = strtotime($peminjaman->tanggal_jatuh_tempo);
        $tglKembali = strtotime($validated['tanggal_dikembalikan']);
        $hariTerlambat = 0;
        $dendaKeterlambatan = 0;

        if ($tglKembali > $tglJatuhTempo) {
            $diffSeconds = $tglKembali - $tglJatuhTempo;
            $hariTerlambat = (int) ceil($diffSeconds / (60 * 60 * 24));
            // Tarif denda keterlambatan: Rp 1.000 / hari
            $dendaKeterlambatan = $hariTerlambat * 1000;
        }

        // 2. Hitung denda kerusakan / hilang
        $dendaKerusakan = 0;
        if ($validated['kondisi_buku'] === 'Rusak Ringan') {
            $dendaKerusakan = 15000;
        } elseif ($validated['kondisi_buku'] === 'Rusak Berat') {
            $dendaKerusakan = 35000;
        } elseif ($validated['kondisi_buku'] === 'Hilang') {
            $dendaKerusakan = 75000;
        }

        $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

        // 3. Tentukan status pembayaran denda
        if ($totalDenda <= 0) {
            $statusPembayaran = 'Bebas Denda';
            $tglBayar = null;
        } elseif ($request->filled('status_bayar_langsung') && $request->status_bayar_langsung === 'lunas') {
            $statusPembayaran = 'Lunas';
            $tglBayar = $validated['tanggal_dikembalikan'];
        } else {
            $statusPembayaran = 'Belum Lunas';
            $tglBayar = null;
        }

        // 4. Generate kode pengembalian unik
        $kodePengembalian = 'TRX-KB-' . date('Ymd') . '-' . str_pad($peminjaman->id_peminjaman, 3, '0', STR_PAD_LEFT);

        // 5. Simpan catatan pengembalian & denda (Create / Update jika ada draft)
        PengembalianDenda::updateOrCreate(
            ['id_peminjaman' => $peminjaman->id_peminjaman],
            [
                'kode_pengembalian' => $kodePengembalian,
                'tanggal_dikembalikan' => $validated['tanggal_dikembalikan'],
                'kondisi_buku' => $validated['kondisi_buku'],
                'keterlambatan_hari' => $hariTerlambat,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'denda_kerusakan' => $dendaKerusakan,
                'total_denda' => $totalDenda,
                'status_pembayaran' => $statusPembayaran,
                'tanggal_pembayaran' => $tglBayar,
                'catatan' => $validated['catatan'] ?? null,
            ]
        );

        // 6. SINKRONISASI: Update status peminjaman menjadi 'Sudah Dikembalikan'
        $peminjaman->status_peminjaman = 'Sudah Dikembalikan';
        $peminjaman->tanggal_kembali = $validated['tanggal_dikembalikan'];
        $peminjaman->save();

        // 7. Jika buku tidak hilang, kembalikan stok fisik buku (+1)
        if ($validated['kondisi_buku'] !== 'Hilang') {
            Buku::where('id_buku', $peminjaman->id_buku)->increment('stok_tersedia', 1);
        }

        $msgDenda = $totalDenda > 0 ? " Total denda: Rp " . number_format($totalDenda, 0, ',', '.') . " ({$statusPembayaran})." : " Bebas denda (Tepat Waktu & Kondisi Baik).";
        return redirect()->route('pengembalian.index')->with('success', "Pengembalian transaksi '{$peminjaman->kode_transaksi}' berhasil disetujui (Approved)!" . $msgDenda);
    }

    // Update status denda dari 'Belum Lunas' menjadi 'Lunas' (Update Pembayaran)
    public function bayarDenda(Request $request, $id)
    {
        $pengembalian = PengembalianDenda::findOrFail($id);

        $pengembalian->status_pembayaran = 'Lunas';
        $pengembalian->tanggal_pembayaran = date('Y-m-d');
        if ($request->filled('catatan_bayar')) {
            $pengembalian->catatan = ($pengembalian->catatan ? $pengembalian->catatan . " | " : "") . "[Lunas]: " . $request->catatan_bayar;
        }
        $pengembalian->save();

        return redirect()->route('pengembalian.index')->with('success', 'Denda sebesar Rp ' . number_format($pengembalian->total_denda, 0, ',', '.') . ' pada transaksi ' . $pengembalian->kode_pengembalian . ' berhasil dilunasi!');
    }

    // Membatalkan / Reset persetujuan pengembalian (Sync balik ke status 'Dipinjam')
    public function destroy(Request $request, $id)
    {
        // $id bisa id_pengembalian atau id_peminjaman
        $pengembalian = PengembalianDenda::where('id_pengembalian', $id)
            ->orWhere('id_peminjaman', $id)
            ->first();

        if (!$pengembalian) {
            return redirect()->back()->with('error', 'Catatan pengembalian tidak ditemukan!');
        }

        $idPeminjaman = $pengembalian->id_peminjaman;
        $kondisi = $pengembalian->kondisi_buku;

        // 1. SINKRONISASI: Kembalikan status peminjaman menjadi 'Dipinjam'
        $peminjaman = Peminjaman::find($idPeminjaman);
        if ($peminjaman) {
            $peminjaman->status_peminjaman = 'Dipinjam';
            $peminjaman->tanggal_kembali = null;
            $peminjaman->save();

            // 2. Kurangi kembali stok buku (karena sebelumnya sempat di-increment saat dikembalikan)
            if ($kondisi !== 'Hilang') {
                Buku::where('id_buku', $peminjaman->id_buku)->decrement('stok_tersedia', 1);
            }
        }

        // 3. Hapus data pengembalian & denda
        $pengembalian->delete();

        return redirect()->route('pengembalian.index')->with('success', "Persetujuan pengembalian berhasil dibatalkan! Transaksi kembali berstatus 'Dipinjam' di kedua modul.");
    }
}
