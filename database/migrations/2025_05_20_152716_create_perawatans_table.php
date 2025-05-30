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
        Schema::create('perawatans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_perawatan');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('jenis_perawatan');
            $table->integer('biaya_perawatan')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_perawatan', ['selesai', 'belum'])->default('belum');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status_ajuan',['pending','disetujui','ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perawatans');
    }
};
