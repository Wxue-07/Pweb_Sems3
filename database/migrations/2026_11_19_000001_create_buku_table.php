<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modul 1: Manajemen Katalog Buku (Winson)
     */
    public function up(): void
    {
        if (!Schema::hasTable('buku')) {
            Schema::create('buku', function (Blueprint $table) {
                $table->id('id_buku');
                $table->string('isbn', 20)->nullable();
                $table->string('judul', 200);
                $table->string('penulis', 100)->nullable();
                $table->string('penerbit', 100)->nullable();
                $table->string('kategori_rak', 50)->nullable();
                $table->text('sinopsis')->nullable();
                $table->integer('stok_tersedia')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
