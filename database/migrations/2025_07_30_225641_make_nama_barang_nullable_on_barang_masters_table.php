<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang_masters', function (Blueprint $table) {
            // Ganti tipe sesuai tipe aslinya, misal string(255)
            $table->string('nama_barang', 255)
                  ->nullable()   // izinkan NULL
                  ->change();    // ubah kolom existing
        });
    }

    public function down()
    {
        Schema::table('barang_masters', function (Blueprint $table) {
            $table->string('nama_barang', 255)
                  ->nullable(false)  // kembalikan jadi NOT NULL
                  ->change();
        });
    }
};
