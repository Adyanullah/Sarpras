<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang_masters')->onDelete('cascade');
            $table->string('kode_barang')->unique();
            $table->year('tahun_perolehan')->nullable();
            $table->string('sumber_dana');
            $table->integer('harga_unit')->nullable();
            $table->string('cv_pengadaan')->nullable();
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->enum('kondisi_barang',['baik','rusak','berat'])->default('baik');
            $table->string('keterangan')->nullable();
            $table->float('sedia')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
