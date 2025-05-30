<?php

namespace Database\Seeders;

use App\Models\AjuanPeminjaman;
use App\Models\AjuanPengadaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AjuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            AjuanPengadaan::create([
                'user_id' => 1,
                'barang_id' => $i,
                'status' => collect(['pending', 'disetujui'])->random(),
            ]);
        }
        for ($i = 1; $i <= 10; $i++) {
            AjuanPeminjaman::create([
                'user_id' => 1,
                'peminjaman_id' => $i,
                'status' => collect(['pending', 'disetujui'])->random(),
            ]);
        }
    }
}
