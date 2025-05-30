<?php

namespace Database\Seeders;

use App\Models\Perawatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerawatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Perawatan::create([
            'tanggal_perawatan' => now()->addDays(1),
            'barang_id' => 1, // Kursi
            'jenis_perawatan' => 'Pengecekan Baut',
            'biaya_perawatan' => 50000,
            'keterangan' => 'Pengecekan kekencangan baut pada kursi kantor',
            'status' => 'selesai',
        ]);

        Perawatan::create([
            'tanggal_perawatan' => now()->addDays(2),
            'barang_id' => 2, // Meja
            'jenis_perawatan' => 'Pengecatan Ulang',
            'biaya_perawatan' => 120000,
            'keterangan' => 'Meja kayu dicat ulang karena lapisan cat mengelupas',
            'status' => 'belum',
        ]);

        Perawatan::create([
            'tanggal_perawatan' => now()->addDays(3),
            'barang_id' => 3, // Papan Tulis
            'jenis_perawatan' => 'Penggantian Permukaan',
            'biaya_perawatan' => 150000,
            'keterangan' => 'Permukaan papan tulis rusak, diganti dengan yang baru',
            'status' => 'belum',
        ]);

        Perawatan::create([
            'tanggal_perawatan' => now()->addDays(4),
            'barang_id' => 4, // Proyektor
            'jenis_perawatan' => 'Pembersihan Lensa',
            'biaya_perawatan' => 80000,
            'keterangan' => 'Membersihkan lensa proyektor dari debu',
            'status' => 'belum',
        ]);

        Perawatan::create([
            'tanggal_perawatan' => now()->addDays(5),
            'barang_id' => 5, // Komputer
            'jenis_perawatan' => 'Penggantian Thermal Paste',
            'biaya_perawatan' => 60000,
            'keterangan' => 'Thermal paste CPU komputer diganti agar tidak overheat',
            'status' => 'selesai',
        ]);
    }
}
