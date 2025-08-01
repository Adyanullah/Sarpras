<?php

namespace App\Exports;

use App\Models\PeminjamanItem;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeminjamanExport implements FromArray, WithHeadings
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function array(): array
    {
        $query = PeminjamanItem::with(['peminjaman','barang.ruangan','barang.barangMaster'])
            ->whereHas('peminjaman', function($q) {
                $q->where('status_ajuan','disetujui');
            })
            ->when($this->start && $this->end, fn($q) =>
                $q->whereHas('peminjaman', fn($q2) =>
                    $q2->whereDate('tanggal_peminjaman','>=',$this->start)
                       ->whereDate('tanggal_peminjaman','<=',$this->end)
                )
            );

        $result = [];
        $no = 1;
        foreach ($query->get() as $item) {
            $pem = $item->peminjaman;
            $tglKembali = $pem->status_peminjaman=='Hilang'
                         ? 'Hilang'
                         : ($pem->tanggal_pengembalian ?? 'Belum Dikembalikan');

            $result[] = [
                $no++,
                $pem->tanggal_peminjaman,
                $tglKembali,
                $item->barang->kode_barang          ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
                $item->barang->barangMaster->jenis_barang ?? '-',
                $item->barang->barangMaster->merk_barang  ?? '-',
                $item->barang->ruangan->nama_ruangan      ?? '-',
                $pem->nama_peminjam                       ?? '-',
                $pem->keterangan ?? '-',
            ];
        }
        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Kode Barang',
            'Nama Barang',
            'Jenis',
            'Merk',
            'Unit',
            'Nama Peminjam',
            'Keterangan',
        ];
    }
}
