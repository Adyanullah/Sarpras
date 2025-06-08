<?php

namespace App\Exports;

use App\Models\PeminjamanItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeminjamanExport implements FromArray, WithHeadings
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        $data = PeminjamanItem::with(['peminjaman', 'barang.ruangan', 'barang.barangMaster'])
            ->whereHas('peminjaman', function ($query) use ($tanggalMulai) {
                $query->whereDate('tanggal_peminjaman', '>=', $tanggalMulai);
            })
            ->get();

        $result = [];
        $no = 1;

        foreach ($data as $item) {
            $result[] = [
                $no++,
                $item->peminjaman->tanggal_peminjaman,
                $item->peminjaman->tanggal_pengembalian,
                $item->barang->kode_barang ?? '-',
                $item->peminjaman->nama_peminjam ?? '-',
                $item->barang->ruangan->nama_ruangan ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pinjam',
            'Batas Pinjam',
            'Kode Barang',
            'Nama Peminjam',
            'Unit',
            'Barang',
        ];
    }
}
