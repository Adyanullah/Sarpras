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
            if ($item->peminjaman->status_peminjaman == 'Hilang')
                $tanggal_pengembalian = 'Hilang';
            else
                $tanggal_pengembalian = $item->peminjaman->tanggal_pengembalian ?? 'Belum Dikembalikan';
            $result[] = [
                $no++,
                $item->peminjaman->tanggal_peminjaman,
                $tanggal_pengembalian,
                $item->barang->kode_barang ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
                $item->barang->barangMaster->jenis_barang ?? '-',
                $item->barang->barangMaster->merk_barang ?? '-',
                $item->barang->ruangan->nama_ruangan ?? '-',
                $item->peminjaman->nama_peminjam ?? '-',
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pinjam',
            'Tanggal Pengembalian',
            'Kode Barang',
            'Nama Barang',
            'Jenis Barang',
            'Merk Barang',
            'Unit',
            'Nama Peminjam',
        ];
    }
}
