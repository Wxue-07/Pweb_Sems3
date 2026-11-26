<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul 4: Transaksi Pengembalian dan Denda (Jolin)
     */
    public function up(): void
    {
        if (!Schema::hasTable('pengembalian_denda')) {
            Schema::create('pengembalian_denda', function (Blueprint $table) {
                $table->id('id_pengembalian');
                $table->string('kode_pengembalian', 30)->unique();
                $table->unsignedBigInteger('id_peminjaman');
                $table->date('tanggal_dikembalikan');
                $table->string('kondisi_buku', 30)->default('Baik'); // Baik, Rusak Ringan, Rusak Berat, Hilang
                $table->integer('keterlambatan_hari')->default(0);
                $table->decimal('denda_keterlambatan', 10, 2)->default(0);
                $table->decimal('denda_kerusakan', 10, 2)->default(0);
                $table->decimal('total_denda', 10, 2)->default(0);
                $table->string('status_pembayaran', 30)->default('Bebas Denda'); // Lunas, Belum Lunas, Bebas Denda
                $table->date('tanggal_pembayaran')->nullable();
                $table->text('catatan')->nullable();

                // Foreign Key
                $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_denda');
    }
};
