<?php
namespace App\Services\Ajuan;

use App\Models\Perawatan;
use Illuminate\Database\Eloquent\Model;

class PerawatanHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var Perawatan $ajuan */
        $ajuan->update(['status_ajuan'=>'disetujui']);
        foreach ($ajuan->perawatanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 0; // sedang dirawat
                $barang->save();
            }
        }
    }

    public function reject(Model $ajuan): void
    {
        $ajuan->update(['status_ajuan'=>'ditolak']);
        foreach ($ajuan->perawatanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 1; // kembalikan
                $barang->save();
            }
        }
    }
}
