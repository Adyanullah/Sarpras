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

        // 1) Ambil semua pengadaan disetujui sejak tanggalMulai, beserta items & barangMaster
        $pengadaans = Pengadaan::with(['barangMaster', 'items'])
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->where('status', 'disetujui')
            ->get();

        $result = [];
        $no = 1;

        foreach ($pengadaans as $p) {
            // 2) Hitung total unit dan total harga
            $jumlahTotal = $p->items->sum('jumlah');
            $totalHarga  = $jumlahTotal * $p->harga_perolehan;

            $result[] = [
                $no++,
                $p->created_at->format('Y-m-d'),
                // Nama, jenis, merk
                $p->nama_barang 
                    ?? optional($p->barangMaster)->nama_barang 
                    ?? '-',
                $p->jenis_barang 
                    ?? optional($p->barangMaster)->jenis_barang 
                    ?? '-',
                $p->merk_barang 
                    ?? optional($p->barangMaster)->merk_barang 
                    ?? '-',
                // Jumlah unit
                $jumlahTotal . ' Unit',
                // Sumber dana & supplier
                $p->sumber_dana ?? '-',
                $p->cv_pengadaan ?? '-',
                // Total harga
                'Rp ' . number_format($totalHarga, 0, ',', '.'),
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
