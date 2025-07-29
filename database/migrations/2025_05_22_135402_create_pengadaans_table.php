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
        Schema::create('pengadaans', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('tipe_pengajuan',['tambah','baru']);
            $table->foreignId('barang_master_id')
                  ->nullable()
                  ->constrained('barang_masters')
                  ->onDelete('set null');
            $table->string('sumber_dana');
            $table->decimal('harga_perolehan',12,2)->nullable();
            $table->string('cv_pengadaan')->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status',['pending','disetujui','ditolak'])->default('pending');
            // untuk tipe 'baru'
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('merk_barang')->nullable();
            $table->string('gambar_barang')->nullable();
            $table->timestamps();
        });
            
        Schema::create('pengadaan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_id')
                ->constrained('pengadaans')
                ->onDelete('cascade');
            $table->foreignId('ruangan_id')
                ->constrained()
                ->onDelete('restrict');
            $table->integer('jumlah');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('pengadaans');
        Schema::table('pengadaans', function (Blueprint $table) {
            $table->integer('jumlah')->after('barang_master_id');
            $table->foreignId('ruangan_id')->after('jumlah')->constrained();
        });
        Schema::dropIfExists('pengadaan_items');
    }
};
