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
        // 1) Pastikan ada baris sama sekali
        if ($rows->isEmpty()) {
            $this->errors[] = "File import kosong.";
            return;
        }

        // 2) Cek header (nama kolom) di baris pertama
        $first       = $rows->first()->toArray();
        $presentCols = array_keys($first);

        $required = [
            'kode_awal',
            'ruangan',
            'jumlah',
            'harga_satuan',
            'keterangan',
            'cv_pengadaan',
            'sumber_dana',
        ];

        $missing = array_diff($required, $presentCols);
        if (! empty($missing)) {
            foreach ($missing as $col) {
                $this->errors[] = "Kolom “{$col}” tidak ditemukan pada file import.";
            }
            return; // stop import jika ada header hilang
        }

        // 3) Proses setiap baris
        foreach ($rows as $idx => $row) {
            $baris      = $idx + 2;
            $prefix     = trim($row['kode_awal']);
            $rawRuangan = trim($row['ruangan']);
            $jumlah     = $row['jumlah'];
            $harga      = $row['harga_satuan'];
            $cv         = trim($row['cv_pengadaan']);
            $sumber     = $row['sumber_dana'];
            $tahun      = $row['tahun_perolehan'] ?? now()->year;
            $keterangan = $row['keterangan'];

            // Validasi wajib non-empty
            if ($prefix === '') {
                $this->errors[] = "Baris {$baris}: kode_awal wajib diisi.";
                continue;
            }
            if ($jumlah === null) {
                $this->errors[] = "Baris {$baris}: jumlah wajib diisi.";
                continue;
            }
            if ($harga === null) {
                $this->errors[] = "Baris {$baris}: harga_satuan wajib diisi.";
                continue;
            }
            if ($cv === '') {
                $this->errors[] = "Baris {$baris}: cv_pengadaan wajib diisi.";
                continue;
            }
            if ($sumber === null) {
                $this->errors[] = "Baris {$baris}: sumber_dana wajib diisi.";
                continue;
            }

            // 4) Resolve ruangan
            $ruangan = is_numeric($rawRuangan)
                ? Ruangan::find($rawRuangan)
                : Ruangan::where('nama_ruangan', $rawRuangan)->first();
            if (! $ruangan) {
                $this->errors[] = "Baris {$baris}: ruangan “{$rawRuangan}” tidak ditemukan.";
                continue;
            }

            // 5) Resolve master (existing vs new)
            $master = BarangMaster::where('kode_barang', 'like', "$prefix%")->first();

            // 6) Build payload pengadaan
            $data = [
                'user_id'          => auth()->id(),
                'sumber_dana'      => $sumber,
                'harga_perolehan'  => $harga,
                'cv_pengadaan'     => $cv,
                'tahun_perolehan'  => $tahun,
                'keterangan'       => $keterangan,
            ];

            if ($master) {
                // existing → tambah
                $data['tipe_pengajuan']   = 'tambah';
                $data['barang_master_id'] = $master->id;
            } else {
                // baru → pakai kolom kode_awal, nama/jenis/merk bisa null
                $data['tipe_pengajuan'] = 'baru';
                $data['kode_barang']    = $prefix;
                $data['nama_barang']    = trim($row['nama_barang']  ?? '') ?: null;
                $data['jenis_barang']   = trim($row['jenis_barang'] ?? '') ?: null;
                $data['merk_barang']    = trim($row['merk_barang']  ?? '') ?: null;
            }

            // 7) Simpan pengadaan & detail item
            $peng = Pengadaan::create($data);
            PengadaanItem::create([
                'pengadaan_id' => $peng->id,
                'ruangan_id'   => $ruangan->id,
                'jumlah'       => $jumlah,
            ]);
        }
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
