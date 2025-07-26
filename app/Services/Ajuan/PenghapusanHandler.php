<?php
namespace App\Services\Ajuan;

use App\Models\Penghapusan;
use Illuminate\Database\Eloquent\Model;

class PenghapusanHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var Penghapusan $ajuan */
        $ajuan->update(['status_ajuan'=>'disetujui']);
        foreach ($ajuan->penghapusanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = -1; // menandai dihapus
                $barang->save();
            }
        }
    }

    public function reject(Model $ajuan): void
    {
        $ajuan->update(['status_ajuan'=>'ditolak']);
        foreach ($ajuan->penghapusanItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 1;
                $barang->save();
            }
        }
    }
}
