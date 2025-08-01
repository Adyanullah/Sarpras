<?php

namespace App\Exports;

use App\Models\PenghapusanItem;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenghapusanExport implements FromArray, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($start, $end)
    {
        $this->startDate = $start;
        $this->endDate   = $end;
    }

    public function array(): array
    {
        $rows = PenghapusanItem::with(['barang.ruangan','barang.barangMaster','penghapusan'])
            ->whereHas('penghapusan', fn($q) => $q->where('status_ajuan','disetujui'))
            ->when($this->startDate && $this->endDate, fn($q) =>
                $q->whereDate('created_at','>=',$this->startDate)
                  ->whereDate('created_at','<=',$this->endDate)
            )->get();

        $result = [];
        $no = 1;
        foreach ($rows as $item) {
            $result[] = [
                $no++,
                $item->created_at->format('Y-m-d'),
                $item->barang->kode_barang              ?? '-',
                $item->barang->barangMaster->nama_barang?? '-',
                $item->barang->barangMaster->jenis_barang?? '-',
                $item->barang->barangMaster->merk_barang ?? '-',
                $item->barang->ruangan->nama_ruangan    ?? '-',
                $item->penghapusan->keterangan          ?? '-',
            ];
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
            'Jenis Barang',
            'Merk Barang',
            'Unit',
            'Alasan Penghapusan',
        ];
    }
}