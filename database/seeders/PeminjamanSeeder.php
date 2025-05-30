<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
// use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Peminjaman::create([
            'tanggal_peminjaman' => now(),
            'tanggal_pengembalian' => now()->addDays(5),
            'nama_peminjam' => 'Rina Marlina',
            'barang_id' => 1,
            'jumlah_barang' => 2,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDays(2),
            'tanggal_pengembalian' => now()->addDays(3),
            'nama_peminjam' => 'Ahmad Yani',
            'barang_id' => 2,
            'jumlah_barang' => 1,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDays(5),
            'tanggal_pengembalian' => now(),
            'nama_peminjam' => 'Siti Rahma',
            'barang_id' => 3,
            'jumlah_barang' => 3,
            'status_peminjaman' => 'Dikembalikan',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now(),
            'tanggal_pengembalian' => now()->addDays(7),
            'nama_peminjam' => 'Budi Santoso',
            'barang_id' => 1,
            'jumlah_barang' => 1,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDay(),
            'tanggal_pengembalian' => now()->addDays(4),
            'nama_peminjam' => 'Wati Lestari',
            'barang_id' => 4,
            'jumlah_barang' => 2,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDays(3),
            'tanggal_pengembalian' => now()->addDays(2),
            'nama_peminjam' => 'Andi Wijaya',
            'barang_id' => 2,
            'jumlah_barang' => 1,
            'status_peminjaman' => 'Dikembalikan',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now(),
            'tanggal_pengembalian' => now()->addDays(5),
            'nama_peminjam' => 'Lina Agustina',
            'barang_id' => 5,
            'jumlah_barang' => 5,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDays(1),
            'tanggal_pengembalian' => now()->addDays(6),
            'nama_peminjam' => 'Dedi Kurniawan',
            'barang_id' => 3,
            'jumlah_barang' => 2,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now(),
            'tanggal_pengembalian' => now()->addDays(3),
            'nama_peminjam' => 'Sari Dewi',
            'barang_id' => 2,
            'jumlah_barang' => 4,
            'status_peminjaman' => 'Dipinjam',
        ]);

        Peminjaman::create([
            'tanggal_peminjaman' => now()->subDays(4),
            'tanggal_pengembalian' => now()->addDay(),
            'nama_peminjam' => 'Ilham Maulana',
            'barang_id' => 4,
            'jumlah_barang' => 2,
            'status_peminjaman' => 'Dikembalikan',
        ]);
    }
}
