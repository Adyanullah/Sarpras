<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruanganList = [
            [
                'kode_ruangan' => 'R001',
                'nama_ruangan' => 'Ruang Rapat Utama',
                'penanggung_jawab' => 'Budi Santoso',
            ],
            [
                'kode_ruangan' => 'R002',
                'nama_ruangan' => 'Laboratorium Komputer',
                'penanggung_jawab' => 'Dewi Lestari',
            ],
            [
                'kode_ruangan' => 'R003',
                'nama_ruangan' => 'Ruang Arsip',
                'penanggung_jawab' => 'Andi Wijaya',
            ],
            [
                'kode_ruangan' => 'R004',
                'nama_ruangan' => 'Gudang Inventaris',
                'penanggung_jawab' => null,
            ],
            [
                'kode_ruangan' => 'R005',
                'nama_ruangan' => 'Ruang Kepala Seksi',
                'penanggung_jawab' => 'Siti Nurhaliza',
            ],
        ];

        foreach ($ruanganList as $ruangan) {
            Ruangan::create($ruangan);
        }
    }
}
