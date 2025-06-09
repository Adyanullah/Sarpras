<?php

namespace App\Exports;

use App\Models\Penghapusan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenghapusanExport implements FromArray, WithHeadings
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        $data = Penghapusan::with(['penghapusanItem.barang.barangMaster'])
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->get();

        $result = [];
        $no = 1;

        foreach ($data as $penghapusan) {
            foreach ($penghapusan->penghapusanItem as $item) {
                $result[] = [
                    $no++,
                    $penghapusan->created_at->format('Y-m-d'),
                    $item->barang->kode_barang ?? '-',
                    $item->barang->barangMaster->nama_barang ?? '-',
                    $penghapusan->keterangan ?? '-',
                ];
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Alasan Penghapusan',
        ];
    }
}

