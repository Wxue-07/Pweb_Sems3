<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianDenda extends Model
{
    use HasFactory;

    // Modul 4: Jolin (Transaksi Pengembalian dan Denda)
    protected $table = 'pengembalian_denda';
    protected $primaryKey = 'id_pengembalian';
    public $timestamps = false;

    protected $fillable = [
        'kode_pengembalian',
        'id_peminjaman',
        'tanggal_dikembalikan',
        'kondisi_buku',
        'keterlambatan_hari',
        'denda_keterlambatan',
        'denda_kerusakan',
        'total_denda',
        'status_pembayaran',
        'tanggal_pembayaran',
        'catatan',
    ];

    // Relasi ke Transaksi Peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }
}
