<?php

namespace Database\Seeders;

use App\Models\BarangMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangDetails = [
            ['kode_barang' => 'KRS', 'nama_barang' => 'Kursi Kantor', 'jenis_barang' => 'Furniture', 'merk_barang' => 'ErgoPro', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'MJA', 'nama_barang' => 'Meja Belajar', 'jenis_barang' => 'Furniture', 'merk_barang' => 'MinimalCraft', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'LTP', 'nama_barang' => 'Laptop ASUS ROG', 'jenis_barang' => 'Elektronik', 'merk_barang' => 'ASUS', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'KMR', 'nama_barang' => 'Kamera Canon', 'jenis_barang' => 'Elektronik', 'merk_barang' => 'Canon', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'PRY', 'nama_barang' => 'Proyektor Epson', 'jenis_barang' => 'Elektronik', 'merk_barang' => 'Epson', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'WTC', 'nama_barang' => 'Whiteboard', 'jenis_barang' => 'Perlengkapan', 'merk_barang' => 'StandardBoard', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'SCN', 'nama_barang' => 'Scanner Canon', 'jenis_barang' => 'Elektronik', 'merk_barang' => 'Canon', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'PRN', 'nama_barang' => 'Printer HP LaserJet', 'jenis_barang' => 'Elektronik', 'merk_barang' => 'HP', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'FLM', 'nama_barang' => 'Filing Cabinet', 'jenis_barang' => 'Furniture', 'merk_barang' => 'SteelStore', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
            ['kode_barang' => 'LEM', 'nama_barang' => 'Lemari Arsip', 'jenis_barang' => 'Furniture', 'merk_barang' => 'ArsipPro', 'gambar_barang' => 'uploads/inventaris/sister.jpg'],
        ];

        foreach ($barangDetails as $detail) {
            BarangMaster::create($detail);
        }
    }
}
