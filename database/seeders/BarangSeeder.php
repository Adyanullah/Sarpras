<?php

namespace Database\Seeders;
use App\Models\Barang;
use App\Models\BarangMaster;
use App\Models\Ruangan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangDetails = BarangMaster::all();
        $ruangans = Ruangan::all();

        if ($barangDetails->isEmpty() || $ruangans->isEmpty()) {
            $this->command->warn('BarangDetail atau Ruangan belum tersedia. Jalankan seedernya terlebih dahulu.');
            return;
        }

        $sumberDanaList = ['BOS', 'DAK', 'Hibah'];
        $kondisiList = ['baik', 'rusak', 'berat'];
        $tahunList = range(2018, now()->year);
        $kodeCounter = [];
        $pemilikList = ['Sekolah', 'pemerintah', 'pinjam'];

        foreach ($barangDetails as $barangDetail) {
            // Jumlah item per jenis
            $jumlahBarang = 20;

            // Tetapkan nilai tetap untuk semua field (selain kode)
            $tahun = fake()->randomElement($tahunList);
            $sumberDana = fake()->randomElement($sumberDanaList);
            $hargaUnit = fake()->numberBetween(50000, 3000000);
            $cv = fake()->company;
            $ruanganId = $ruangans->random()->id;
            $pemilik = fake()->randomElement($pemilikList);

            // Inisialisasi counter untuk kode_barang unik
            if (!isset($kodeCounter[$barangDetail->kode_barang])) {
                $kodeCounter[$barangDetail->kode_barang] = 1;
            }

            for ($i = 0; $i < $jumlahBarang; $i++) {
                $kodeBarangUnik = $barangDetail->kode_barang . '-' . str_pad($kodeCounter[$barangDetail->kode_barang]++, 5, '0', STR_PAD_LEFT);

                Barang::create([
                    'barang_id' => $barangDetail->id,
                    'kode_barang' => $kodeBarangUnik,
                    'tahun_perolehan' => $tahun,
                    'sumber_dana' => $sumberDana,
                    'harga_unit' => $hargaUnit,
                    'cv_pengadaan' => $cv,
                    'ruangan_id' => $ruanganId,
                    'kondisi_barang' => fake()->randomElement($kondisiList),
                    'kepemilikan_barang' => $pemilik,
                ]);
            }
        }
    }
}
