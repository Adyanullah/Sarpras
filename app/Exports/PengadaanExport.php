<?php

namespace App\Exports;

use App\Models\Pengadaan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengadaanExport implements FromArray, WithHeadings
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        $pengadaans = Pengadaan::with('barangMaster')
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->get();

        $result = [];
        $no = 1;

        foreach ($pengadaans as $pengadaan) {
            $result[] = [
                $no++,
                $pengadaan->created_at->format('Y-m-d'),
                $pengadaan->nama_barang ?? ($pengadaan->barangMaster->nama_barang ?? '-'),
                $pengadaan->jenis_barang ?? ($pengadaan->barangMaster->jenis_barang ?? '-'),
                $pengadaan->merk_barang ?? ($pengadaan->barangMaster->merk_barang ?? '-'),
                $pengadaan->jumlah . ' Unit',
                $pengadaan->sumber_dana ?? '-',
                $pengadaan->cv_pengadaan ?? '-',
                'Rp ' . number_format($pengadaan->harga_perolehan, 0, ',', '.'),
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
