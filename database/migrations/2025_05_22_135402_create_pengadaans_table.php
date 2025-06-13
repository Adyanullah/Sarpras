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
        // Schema::create('pengadaans', function (Blueprint $table) {
        //     $table->id();

        //     // User yang mengajukan permintaan
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');

        //     // Barang yang ingin ditambahkan jumlahnya (nullable jika barang baru)
        //     $table->foreignId('barang_id')->nullable()->constrained('barangs')->onDelete('set null');

        //     // Jika pengajuan barang baru, beberapa data disiapkan
        //     $table->string('nama_barang')->nullable();
        //     $table->string('jenis_barang')->nullable();
        //     $table->string('merk_barang')->nullable();
        //     $table->year('tahun_perolehan')->nullable();
        //     $table->enum('sumber_dana', ['BOS', 'DAK', 'Hibah'])->nullable();
        //     $table->decimal('harga_perolehan', 15, 2)->nullable();
        //     $table->string('cv_pengadaan')->nullable();
        //     $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->onDelete('set null');
        //     $table->enum('kondisi_barang', ['baik', 'rusak', 'berat'])->nullable();
        //     $table->string('kepemilikan_barang')->nullable();
        //     $table->string('penanggung_jawab')->nullable();
        //     $table->text('gambar_barang')->nullable();

        //     // Jumlah barang yang diajukan
        //     $table->integer('jumlah');

        //     // Tipe pengajuan: tambah stok atau barang baru
        //     $table->enum('tipe_pengajuan', ['tambah', 'baru']);

        //     // Status pengajuan
        //     $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');

        //     // Catatan opsional dari waka
        //     $table->text('catatan')->nullable();

        //     $table->timestamps();
        // });

        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User pengaju
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable(); // Catatan dari Waka
            // Barang lama (penambahan jumlah), nullable jika barang baru
            $table->foreignId('barang_master_id')->nullable()->constrained('barang_masters')->onDelete('set null');

            // Data barang baru jika `barang_master_id` null
            $table->string('kode_barang')->nullable()->unique();
            $table->string('nama_barang')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('merk_barang')->nullable();

            // Informasi umum
            $table->year('tahun_perolehan')->nullable();
            $table->string('sumber_dana')->nullable();
            $table->integer('harga_perolehan')->nullable();
            $table->string('cv_pengadaan')->nullable();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->onDelete('set null');
            $table->enum('kondisi_barang', ['baik', 'rusak', 'berat'])->nullable();
            $table->string('keterangan')->nullable();
            $table->text('gambar_barang')->nullable();

            // Info umum pengajuan
            $table->integer('jumlah');
            $table->enum('tipe_pengajuan', ['tambah', 'baru']); // barang lama atau baru

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaans');
    }
};
