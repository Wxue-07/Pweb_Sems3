<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnggotaController extends Controller
{
    /**
     * Modul 2: Manajemen Anggota Perpustakaan
     * Kontributor: Budi Yanto
     */

    // Menampilkan daftar anggota perpustakaan
    public function index(Request $request)
    {
        $query = Anggota::query();

        // Fitur Pencarian (Nama, Nomor Anggota, NIM/NIDN, Email, Prodi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_anggota', 'like', "%{$search}%")
                  ->orWhere('nim_nidn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan jenis anggota (Mahasiswa/Dosen/Staff)
        if ($request->filled('jenis_anggota')) {
            $query->where('jenis_anggota', $request->jenis_anggota);
        }

        // Filter berdasarkan status keaktifan (Aktif/Nonaktif/Lulus)
        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        // Urutkan dari data anggota terbaru
        $anggota = $query->orderBy('id_anggota', 'desc')->get();

        // Hitung statistik ringkas untuk kartu atas
        $stats = [
            'total_anggota' => Anggota::count(),
            'total_aktif' => Anggota::where('status_aktif', 'Aktif')->count(),
            'total_mahasiswa' => Anggota::where('jenis_anggota', 'Mahasiswa')->count(),
            'total_dosen_staff' => Anggota::whereIn('jenis_anggota', ['Dosen', 'Staff'])->count(),
        ];

        return view('anggota.index', compact('anggota', 'stats'));
    }

    // Menyimpan data anggota baru ke database (Insert)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'jenis_anggota' => 'required|string|max:30',
            'nim_nidn' => 'nullable|string|max:30',
            'program_studi' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_aktif' => 'nullable|string|max:30',
        ]);

        // Buat nomor anggota otomatis jika tidak diisi manual
        $prefix = ($request->jenis_anggota === 'Dosen') ? 'DSN' : 'MHS';
        $nomorOtomatis = $prefix . '-' . date('Y') . '-' . str_pad((Anggota::count() + 1), 3, '0', STR_PAD_LEFT);
        $validated['nomor_anggota'] = $request->filled('nomor_anggota') ? $request->nomor_anggota : $nomorOtomatis;
        $validated['status_aktif'] = $request->status_aktif ?? 'Aktif';
        $validated['tanggal_bergabung'] = date('Y-m-d');

        $anggota = Anggota::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Anggota baru berhasil didaftarkan!',
                'data' => $anggota
            ]);
        }

        return redirect()->route('anggota.index')->with('success', 'Data anggota "' . $anggota->nama_lengkap . '" berhasil didaftarkan!');
    }

    // Mengambil detail 1 anggota (Read Detail)
    public function show($id)
    {
        $anggota = Anggota::with('peminjaman.buku')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $anggota
        ]);
    }

    // Mengupdate data profil anggota (Update: no HP, Email, status, dll)
    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'jenis_anggota' => 'required|string|max:30',
            'nim_nidn' => 'nullable|string|max:30',
            'program_studi' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_aktif' => 'required|string|max:30',
        ]);

        $anggota->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil diperbarui!',
                'data' => $anggota
            ]);
        }

        return redirect()->route('anggota.index')->with('success', 'Data anggota "' . $anggota->nama_lengkap . '" berhasil diperbarui!');
    }

    // Menghapus data anggota atau menonaktifkan yang sudah lulus/keluar (Delete)
    public function destroy(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);

        // Cek apakah anggota sedang meminjam buku yang belum dikembalikan
        $pinjamAktif = DB::table('peminjaman')
            ->where('id_anggota', $id)
            ->where('status_peminjaman', 'Dipinjam')
            ->exists();

        if ($pinjamAktif) {
            $msg = 'Anggota tidak dapat dihapus karena masih memiliki tanggungan peminjaman buku yang sedang aktif!';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $nama = $anggota->nama_lengkap;
        $anggota->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data anggota '{$nama}' berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('anggota.index')->with('success', "Data anggota '{$nama}' berhasil dihapus dari sistem!");
    }
}
