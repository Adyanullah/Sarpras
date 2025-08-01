<?php

namespace App\Exports;

use App\Models\PerawatanItem;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerawatanExport implements FromArray, WithHeadings
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
        $query = PerawatanItem::with('barang.ruangan','perawatan','barang.barangMaster')
            ->whereHas('perawatan', function($q) {
                $q->where('status_ajuan','disetujui');
            })
            ->when($this->startDate && $this->endDate, fn($q) =>
                $q->whereHas('perawatan', fn($q2) =>
                    $q2->whereDate('tanggal_perawatan','>=',$this->startDate)
                       ->whereDate('tanggal_perawatan','<=',$this->endDate)
                )
            );

        $result = [];
        $no = 1;
        foreach ($query->get() as $data) {
            $p = $data->perawatan;
            $result[] = [
                $no++,
                $p->tanggal_perawatan,
                $p->tanggal_selesai       ?? 'Belum Selesai',
                $data->barang->kode_barang ?? '-',
                $data->barang->barangMaster->nama_barang ?? '-',
                $data->barang->ruangan->nama_ruangan       ?? '-',
                $p->jenis_perawatan,
                'Rp. '.number_format($p->biaya_perawatan, 0, ',', '.'),
                $p->keterangan               ?? '-',
            ];
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
            'Biaya',
            'Keterangan',
        ];
    }
}
