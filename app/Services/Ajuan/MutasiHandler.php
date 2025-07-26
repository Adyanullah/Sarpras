<?php
namespace App\Services\Ajuan;

use App\Models\Mutasi;
use Illuminate\Database\Eloquent\Model;

class MutasiHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var Mutasi $ajuan */
        $ajuan->update([
            'status_ajuan'  => 'disetujui',
            'status_mutasi' => 'selesai',
        ]);
        $tujuan = $ajuan->tujuan;
        foreach ($ajuan->mutasiItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->ruangan_id = $tujuan;
                $barang->sedia      = 1;
                $barang->save();
            }
        }
    }

    public function reject(Model $ajuan): void
    {
        $ajuan->update(['status_ajuan'=>'ditolak']);
        foreach ($ajuan->mutasiItem as $item) {
            $barang = $item->barang;
            if ($barang) {
                $barang->sedia = 1;
                $barang->save();
            }
        }
    }
}
