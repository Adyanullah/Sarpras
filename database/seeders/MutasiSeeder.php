<?php

namespace Database\Seeders;

use App\Models\Mutasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MutasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mutasi::create([
            'tanggal_mutasi' => now()->subDays(3),
            'nama_mutasi' => 'Pemindahan Kursi ke Ruang Guru',
            'barang_id' => 1,
            'jumlah_barang' => 5,
            'tujuan' => 2,
            'keterangan' => 'Kursi dipindahkan untuk menambah fasilitas di ruang guru',
        ]);

        Mutasi::create([
            'tanggal_mutasi' => now()->subDays(1),
            'nama_mutasi' => 'Pemindahan Meja ke Lab Komputer',
            'barang_id' => 2,
            'jumlah_barang' => 5,
            'tujuan' => 4,
            'keterangan' => 'Meja digunakan untuk menambah kapasitas kerja di lab komputer',
        ]);
    }
}
