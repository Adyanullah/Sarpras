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
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_mutasi');
            $table->string('nama_mutasi');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_barang');
            $table->integer('tujuan');
            $table->text('keterangan')->nullable();
            $table->enum('status_mutasi', ['Batal', 'pending', 'Dikirim'])->default('pending');
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
        Schema::dropIfExists('mutasis');
    }
};
