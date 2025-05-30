<?php

namespace App\Exports;

use App\Models\Mutasi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class MutasiExport implements FromCollection
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);
        return Mutasi::with(['barang.ruangan', 'ajuan'])
            ->whereDate('tanggal_mutasi', '>=', $tanggalMulai)
            ->get();
    }
}

