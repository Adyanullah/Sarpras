<?php
namespace App\Services\Ajuan;

use App\Models\Pengadaan;
use App\Models\BarangMaster;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PengadaanHandler implements AjuanHandlerInterface
{
    public function approve(Model $ajuan): void
    {
        /** @var Pengadaan $ajuan */

        // 1) Buat atau ambil master barang
        if ($ajuan->tipe_pengajuan === 'baru') {
            $master = BarangMaster::create([
                'kode_barang'   => $ajuan->kode_barang,
                'nama_barang'   => $ajuan->nama_barang,
                'jenis_barang'  => $ajuan->jenis_barang,
                'merk_barang'   => $ajuan->merk_barang,
                'gambar_barang' => $ajuan->gambar_barang,
            ]);
        } else {
            $master = $ajuan->barangMaster;
        }

        // 2) Siapkan batch insert stok
        $prefix = $master->kode_barang;

        // Cari kode terakhir di table barangs
        $last = Barang::where('kode_barang', 'like', $prefix.'-%')
            ->orderByDesc('kode_barang')
            ->first();

        $lastNumber = $last
            ? (int) substr($last->kode_barang, strlen($prefix) + 1)
            : 0;

        $nextNumber = $lastNumber + 1;
        $rows = [];

        // Untuk setiap lokasi/jumlah
        foreach ($ajuan->items as $item) {
            for ($i = 0; $i < $item->jumlah; $i++) {
                $kode = sprintf('%s-%05d', $prefix, $nextNumber++);

                $rows[] = [
                    'barang_id'        => $master->id,
                    'kode_barang'      => $kode,
                    'tahun_perolehan'  => $ajuan->tahun_perolehan,
                    'sumber_dana'      => $ajuan->sumber_dana,
                    'harga_unit'       => $ajuan->harga_perolehan,
                    'cv_pengadaan'     => $ajuan->cv_pengadaan,
                    'ruangan_id'       => $item->ruangan_id,
                    'kondisi_barang'   => 'baik',
                    'keterangan'       => $ajuan->keterangan,
                    'sedia'            => 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        // 3) Bulk insert barangs baru
        if (!empty($rows)) {
            Barang::insert($rows);
        }

        // 4) Tandai pengadaan sudah disetujui
        $ajuan->update(['status' => 'disetujui']);
    }

    public function reject(Model $ajuan): void
    {
        /** @var Pengadaan $ajuan */
        // Hapus gambar jika ada
        if ($ajuan->gambar_barang && Storage::exists($ajuan->gambar_barang)) {
            Storage::delete($ajuan->gambar_barang);
        }

        // Tandai sebagai ditolak
        $ajuan->update(['status' => 'ditolak']);
    }
}
