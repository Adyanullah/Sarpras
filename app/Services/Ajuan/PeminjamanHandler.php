<?php
namespace App\Services\Ajuan;

use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Model;

class PeminjamanHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var Peminjaman $ajuan */
        $ajuan->update([
            'status_ajuan'      => 'disetujui',
            'status_peminjaman' => 'Dipinjam',
        ]);
        foreach ($ajuan->peminjamanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 0.1; // dianggap dipinjam
                $barang->save();
            }
        }
    }

    public function reject(Model $ajuan): void
    {
        $ajuan->update(['status_ajuan' => 'ditolak']);
        foreach ($ajuan->peminjamanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 1; // kembalikan ke tersedia
                $barang->save();
            }
        }
    }
}
