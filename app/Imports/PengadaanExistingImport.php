<?php

namespace App\Imports;

use App\Models\Pengadaan;
use App\Models\PengadaanItem;
use App\Models\BarangMaster;
use App\Models\Ruangan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow};

class PengadaanExistingImport implements ToCollection, WithHeadingRow
{
    /** @var string[] */
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $baris      = $index + 2;
            $prefix     = trim($row['kode_awal'] ?? '');
            $rawRuangan = trim($row['ruangan']   ?? '');
            $jumlah     = $row['jumlah'] ?? null;
            $harga      = $row['harga_perolehan'] ?? null;
            $cv         = trim($row['cv_pengadaan'] ?? '');
            $sumber     = $row['sumber_dana'] ?? null;
            $tahun      = $row['tahun_perolehan'] ?? now()->year;
            $keterangan = $row['keterangan'] ?? null;

            // 1) Cek kode_awal
            $master = BarangMaster::where('kode_barang', 'like', "$prefix%")->first();
            if (! $master) {
                $this->errors[] = "Baris {$baris}: kode_awal “{$prefix}” tidak ditemukan di master.";
                continue;
            }

            // 2) Cek ruangan
            $ruangan = is_numeric($rawRuangan)
                ? Ruangan::find($rawRuangan)
                : Ruangan::where('nama_ruangan', $rawRuangan)->first();
            if (! $ruangan) {
                $this->errors[] = "Baris {$baris}: ruangan “{$rawRuangan}” tidak ditemukan.";
                continue;
            }

            // 3) Cek supplier (cv_pengadaan)
            if ($cv === '') {
                $this->errors[] = "Baris {$baris}: cv_pengadaan wajib diisi.";
                continue;
            }

            // 4) Simpan only jika semua valid
            $peng = Pengadaan::create([
                'user_id'          => auth()->id(),
                'tipe_pengajuan'   => 'tambah',
                'barang_master_id' => $master->id,
                'sumber_dana'      => $sumber,
                'harga_perolehan'  => $harga,
                'cv_pengadaan'     => $cv,
                'tahun_perolehan'  => $tahun,
                'keterangan'       => $keterangan,
            ]);

            PengadaanItem::create([
                'pengadaan_id' => $peng->id,
                'ruangan_id'   => $ruangan->id,
                'jumlah'       => $jumlah,
            ]);
        }
    }

    /**
     * Dipanggil oleh controller untuk mengambil semua pesan error.
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
