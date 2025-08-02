<?php

namespace App\Exports;

use App\Models\Pengadaan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengadaanExport implements FromArray, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function array(): array
    {
        $query = Pengadaan::with(['barangMaster','items'])
            ->where('status','disetujui')
            ->when($this->startDate && $this->endDate, fn($q) =>
                $q->whereDate('created_at','>=',$this->startDate)
                  ->whereDate('created_at','<=',$this->endDate)
            );

        $result = [];
        $no     = 1;
        foreach ($query->get() as $p) {
            $jumlah = $p->items->sum('jumlah');
            $harga  = $jumlah * $p->harga_perolehan;

            $namaBarang = optional($p->barangMaster)->nama_barang
                ?: ($p->nama_barang
                    ? $p->nama_barang
                    : '-'
                  );

            $jenisBarang = optional($p->barangMaster)->jenis_barang
                ?: ($p->jenis_barang
                    ? $p->jenis_barang
                    : '-'
                  );

            $merkBarang = optional($p->barangMaster)->merk_barang
                ?: ($p->merk_barang
                    ? $p->merk_barang
                    : '-'
                  );

            $result[] = [
                $no++,
                $p->tahun_perolehan,
                $namaBarang,
                $jenisBarang,
                $merkBarang   ?? '-',
                "{$jumlah} Unit",
                $p->sumber_dana                  ?? '-',
                $p->cv_pengadaan                 ?? '-',
                'Rp ' . number_format($harga, 0, ',', '.'),
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengadaan',
            'Nama Barang',
            'Jenis Barang',
            'Merk / Spesifikasi',
            'Jumlah Barang',
            'Sumber Dana',
            'Supplier',
            'Total Harga',
        ];
    }
}
