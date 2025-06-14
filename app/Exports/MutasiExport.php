<?php

namespace App\Exports;

use App\Models\MutasiItem;
use App\Models\Ruangan;
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

        $data = MutasiItem::with(['mutasi', 'barang.ruangan', 'barang.barangMaster'])
            ->whereHas('mutasi', function ($query) use ($tanggalMulai) {
                $query->whereDate('tanggal_mutasi', '>=', $tanggalMulai);
            })
            ->get();
        $ruangans = Ruangan::pluck('nama_ruangan', 'id')->toArray();
        $result = [];
        $no = 1;

        foreach ($data as $item) {
            $result[] = [
                $no++,
                $item->mutasi->tanggal_mutasi,
                $item->barang->kode_barang ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
                $item->barang->barangMaster->jenis_barang ?? '-',
                $item->barang->barangMaster->merk_barang ?? '-',
                $ruangans[$item->mutasi->asal],
                $ruangans[$item->mutasi->tujuan],
                $item->mutasi->keterangan ?? '-',
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
            'Jenis Barang',
            'Merk Barang',
            'Dari Unit',
            'Ke Unit',
            'Keterangan',
        ];
    }
}
