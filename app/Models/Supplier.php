<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Modul 5: Rowin Journandez (Data Supplier)
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    public $timestamps = false;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'kontak_person',
        'telepon',
        'email',
        'alamat',
    ];

    // Relasi ke transaksi pengadaan buku
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'id_supplier', 'id_supplier');
    }
}
