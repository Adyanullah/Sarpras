<?php
namespace App\Services\Ajuan;

use App\Models\BarangRusak;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BarangRusakHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var BarangRusak $ajuan */
        $ajuan->update(['status_ajuan'=>'disetujui']);
        $barang = $ajuan->barang;
        if ($barang) {
            $barang->kondisi_barang = $ajuan->kondisi_barang;
            $barang->save();
        }
    }

    public function reject(Model $ajuan): void
    {
        // Hapus foto rusak jika ada
        if ($ajuan->gambar_barang && Storage::exists($ajuan->gambar_barang)) {
            Storage::delete($ajuan->gambar_barang);
        }
        $ajuan->update(['status_ajuan'=>'ditolak']);
    }
}
