<?php

namespace App\Exports;

use App\Models\Mutasi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MutasiExport implements FromArray, WithHeadings
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        $data = Mutasi::with(['barang.barangMaster', 'barang.ruangan', 'tujuanRuangan'])
            ->whereDate('tanggal_mutasi', '>=', $tanggalMulai)
            ->get();

        $result = [];
        $no = 1;

        foreach ($data as $item) {
            $result[] = [
                $no++,
                $item->tanggal_mutasi,
                $item->barang->kode_barang ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
                $item->barang->ruangan->nama_ruangan ?? '-',
                optional($item->tujuanRuangan)->nama_ruangan ?? '-',
                $item->jumlah_barang ?? '-',
                $item->keterangan ?? '-',
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Mutasi',
            'Kode Barang',
            'Nama Barang',
            'Dari Unit',
            'Ke Unit',
            'Jumlah',
            'Keterangan',
        ];
    }
}
