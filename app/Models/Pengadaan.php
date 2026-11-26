<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    // Modul 5: Rowin Journandez (Pengadaan Buku Baru)
    protected $table = 'pengadaan_buku';
    protected $primaryKey = 'id_pengadaan';
    public $timestamps = false;

    protected $fillable = [
        'kode_pengadaan',
        'id_supplier',
        'id_buku',
        'tanggal_pengadaan',
        'jumlah_masuk',
        'harga_satuan',
        'total_biaya',
        'sumber_dana',
        'keterangan',
    ];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    // Relasi ke Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
