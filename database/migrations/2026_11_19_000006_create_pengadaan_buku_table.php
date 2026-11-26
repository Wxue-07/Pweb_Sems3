<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul 5: Pengadaan Buku Baru (Rowin Journandez)
     */
    public function up(): void
    {
        if (!Schema::hasTable('pengadaan_buku')) {
            Schema::create('pengadaan_buku', function (Blueprint $table) {
                $table->id('id_pengadaan');
                $table->string('kode_pengadaan', 30)->unique();
                $table->unsignedBigInteger('id_supplier');
                $table->unsignedBigInteger('id_buku');
                $table->date('tanggal_pengadaan');
                $table->integer('jumlah_masuk')->default(1);
                $table->decimal('harga_satuan', 12, 2)->default(0);
                $table->decimal('total_biaya', 12, 2)->default(0);
                $table->string('sumber_dana', 50)->default('Kas Perpustakaan'); // APBN / Yayasan, Hibah / Donasi, Kas Perpustakaan
                $table->text('keterangan')->nullable();

                // Foreign Keys
                $table->foreign('id_supplier')->references('id_supplier')->on('supplier')->onDelete('cascade');
                $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_buku');
    }
};
