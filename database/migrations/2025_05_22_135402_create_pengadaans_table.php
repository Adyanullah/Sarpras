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
        Schema::create('pengadaans', function(Blueprint $t){
            $t->id();
            $t->foreignId('user_id')->constrained();
            $t->enum('tipe_pengajuan',['tambah','baru']);
            $t->foreignId('barang_master_id')
                  ->nullable()
                  ->constrained('barang_masters')
                  ->onDelete('set null');
            $t->string('sumber_dana');
            $t->decimal('harga_perolehan',12,2);
            $t->string('cv_pengadaan');
            $t->year('tahun_perolehan');
            $t->text('keterangan')->nullable();
            $t->enum('status',['pending','disetujui','ditolak'])->default('pending');
            // untuk tipe 'baru'
            $t->string('kode_barang')->nullable();
            $t->string('nama_barang')->nullable();
            $t->string('jenis_barang')->nullable();
            $t->string('merk_barang')->nullable();
            $t->string('gambar_barang')->nullable();
            $t->timestamps();
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
