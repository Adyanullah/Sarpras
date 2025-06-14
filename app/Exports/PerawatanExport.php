<?php

namespace App\Exports;

use App\Models\Perawatan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerawatanExport implements FromArray, WithHeadings
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);
        $data = Perawatan::with('perawatanItem.barang.ruangan', 'user')
            ->where('tanggal_perawatan', '>=', $tanggalMulai)
            ->get();

        $result = [];
        $no = 1;

        foreach ($data as $perawatan) {
            foreach ($perawatan->perawatanItem as $item) {
                $result[] = [
                    $no++,
                    $perawatan->tanggal_perawatan,
                    $perawatan->perawatan->tanggal_selesai ?? 'Belum Selesai',
                    $item->barang->kode_barang ?? '-',
                    $item->barang->barangMaster->nama_barang ?? '-',
                    $item->barang->ruangan->nama_ruangan ?? '-',
                    $perawatan->jenis_perawatan,
                    $perawatan->biaya_perawatan,
                    $perawatan->keterangan,
                ];
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Perawatan',
            'Tanggal Selesai',
            'Kode Barang',
            'Nama Barang',
            'Unit',
            'Jenis Perawatan',
            'Biaya (Rp)',
            'Keterangan',
        ];
    }
}
