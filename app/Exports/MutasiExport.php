<?php

namespace App\Exports;

use App\Models\MutasiItem;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MutasiExport implements FromArray, WithHeadings
{
    protected $start, $end, $ruangans;

    public function __construct($start, $end)
    {
        $this->start    = $start;
        $this->end      = $end;
        $this->ruangans = Ruangan::pluck('nama_ruangan','id')->toArray();
    }

    public function array(): array
    {
        $query = MutasiItem::with(['mutasi','barang.ruangan','barang.barangMaster'])
            ->whereHas('mutasi', function($q) {
                $q->where('status_ajuan','disetujui');
            })
            ->when($this->start && $this->end, fn($q) =>
                $q->whereHas('mutasi', fn($q2) =>
                    $q2->whereDate('tanggal_mutasi','>=',$this->start)
                       ->whereDate('tanggal_mutasi','<=',$this->end)
                )
            );

        $result = []; $no = 1;
        foreach ($query->get() as $item) {
            $m = $item->mutasi;
            $result[] = [
                $no++,
                $m->tanggal_mutasi,
                $item->barang->kode_barang              ?? '-',
                $item->barang->barangMaster->nama_barang ?? '-',
                $item->barang->barangMaster->jenis_barang ?? '-',
                $item->barang->barangMaster->merk_barang  ?? '-',
                $this->ruangans[$m->asal]               ?? '-',
                $this->ruangans[$m->tujuan]             ?? '-',
                $m->keterangan                             ?? '-',
            ];
        }
        return $result;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pindah',
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

