<?php

namespace App\Exports;

use App\Models\Penghapusan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PenghapusanExport implements FromCollection
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        return Penghapusan::with(['barang.ruangan', 'ajuan'])
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->whereHas('ajuan', function ($q) {
                $q->where('status', 'pending');
            })->get();
    }
}
