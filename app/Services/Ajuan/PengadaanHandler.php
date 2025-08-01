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
        // --- 1) Tentukan master barang ---
        if ($ajuan->tipe_pengajuan === 'tambah') {
            // murni tambah unit ke master yang sudah ada
            $master = $ajuan->barangMaster;

        } else {
            // tipe 'baru' — cek dulu apakah master sudah ada
            $existing = BarangMaster::where('kode_barang', $ajuan->kode_barang)->first();
            if ($existing) {
                // sudah ada, treat as tambah
                $master = $existing;
            } else {
                // belum ada, buat master baru
                $master = BarangMaster::create([
                    'kode_barang'   => $ajuan->kode_barang,
                    'nama_barang'   => $ajuan->nama_barang,
                    'jenis_barang'  => $ajuan->jenis_barang,
                    'merk_barang'   => $ajuan->merk_barang,
                    'gambar_barang' => $ajuan->gambar_barang,
                ]);
            }
        }

        // --- 2) Convert semua ajuan “baru” lainnya jadi “tambah” ---
        Pengadaan::where('tipe_pengajuan', 'baru')
            ->where('status', 'pending')
            ->where('kode_barang', $master->kode_barang)
            ->where('id', '<>', $ajuan->id)
            ->update([
                'tipe_pengajuan'   => 'tambah',
                'barang_master_id' => $master->id,
                // jika ingin clear field-field khusus ‘baru’, uncomment di bawah:
                // 'kode_barang'   => null,
                // 'nama_barang'   => null,
                // 'jenis_barang'  => null,
                // 'merk_barang'   => null,
                // 'gambar_barang' => null,
            ]);

        // --- 3) Siapkan batch insert stok barang ---
        $prefix = $master->kode_barang;
        $last   = Barang::where('kode_barang', 'like', "$prefix-%")
                        ->orderByDesc('kode_barang')
                        ->first();

        $lastNumber = $last
            ? (int) substr($last->kode_barang, strlen($prefix) + 1)
            : 0;
        $nextNumber = $lastNumber + 1;

        $rows = [];
        foreach ($ajuan->items as $item) {
            for ($i = 0; $i < $item->jumlah; $i++) {
                $rows[] = [
                    'barang_id'       => $master->id,
                    'kode_barang'     => sprintf('%s-%05d', $prefix, $nextNumber++),
                    'tahun_perolehan' => $ajuan->tahun_perolehan,
                    'sumber_dana'     => $ajuan->sumber_dana,
                    'harga_unit'      => $ajuan->harga_perolehan,
                    'cv_pengadaan'    => $ajuan->cv_pengadaan,
                    'ruangan_id'      => $item->ruangan_id,
                    'kondisi_barang'  => 'baik',
                    'keterangan'      => $ajuan->keterangan,
                    'sedia'           => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        // --- 4) Bulk insert unit baru ---
        if (! empty($rows)) {
            Barang::insert($rows);
        }

        // --- 5) Tandai ajuan sudah disetujui ---
        $ajuan->update(['status' => 'disetujui']);
    }

    public function reject(Model $ajuan): void
    {
        /** @var Pengadaan $ajuan */
        // Hapus gambar jika ada
        if ($ajuan->gambar_barang && Storage::exists($ajuan->gambar_barang)) {
            Storage::delete($ajuan->gambar_barang);
        }
        // Tandai ajuan ditolak
        $ajuan->status = 'ditolak';
        $ajuan->save();
    }
}
