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
        $ruangans     = Ruangan::all();

        if ($barangDetails->isEmpty() || $ruangans->isEmpty()) {
            $this->command->warn('BarangMaster atau Ruangan belum tersedia. Jalankan seedernya terlebih dahulu.');
            return;
        }

        $sumberDanaList = ['BOS', 'DAK', 'Hibah'];
        $kondisiList    = ['baik', 'rusak', 'berat'];
        $kodeCounter    = [];
        $pemilikList    = ['Budi Santoso', 'Dewi Lestari', 'Andi Wijaya', 'Siti Nurhaliza', 'Ahmad Yani', 'Rina Marlina', 'Siti Rahma'];

        foreach ($barangDetails as $barangDetail) {
            // Jumlah item per jenis
            $jumlahBarang = 20;

            // Pilihan statis per seed run
            $sumberDana = fake()->randomElement($sumberDanaList);
            $hargaUnit  = fake()->numberBetween(50_000, 3_000_000);
            $cv         = fake()->company();
            $ruanganId  = $ruangans->random()->id;
            $pemilik    = fake()->randomElement($pemilikList);

            // Inisialisasi counter untuk kode_barang unik
            if (! isset($kodeCounter[$barangDetail->kode_barang])) {
                $kodeCounter[$barangDetail->kode_barang] = 1;
            }

            for ($i = 0; $i < $jumlahBarang; $i++) {
                // 1) Kode unik
                $kodeBarangUnik = $barangDetail->kode_barang
                                . '-'
                                . str_pad($kodeCounter[$barangDetail->kode_barang]++, 5, '0', STR_PAD_LEFT);

                // 2) Tanggal perolehan acak antara 2018-01-01 dan hari ini
                $tanggalPerolehan = fake()
                    ->dateTimeBetween('2018-01-01', now())
                    ->format('Y-m-d');

                Barang::create([
                    'barang_id'         => $barangDetail->id,
                    'kode_barang'       => $kodeBarangUnik,
                    // Jika kolomnya masih bernama 'tahun_perolehan' tapi tipenya DATE,
                    // masukkan $tanggalPerolehan ke situ.
                    // Atau, jika kamu sudah mengganti nama kolom menjadi 'tanggal_perolehan', ganti key-nya:
                    'tahun_perolehan'   => $tanggalPerolehan,
                    'sumber_dana'       => $sumberDana,
                    'harga_unit'        => $hargaUnit,
                    'cv_pengadaan'      => $cv,
                    'ruangan_id'        => $ruanganId,
                    'kondisi_barang'    => fake()->randomElement($kondisiList),
                    'keterangan'        => $pemilik,
                ]);
            }
        }
    }
}
