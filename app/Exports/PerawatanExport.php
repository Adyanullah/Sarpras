<?php

namespace App\Exports;

use App\Models\Perawatan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PerawatanExport implements FromCollection
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);
        return Perawatan::with('barang.ruangan', 'ajuan')
            ->where('tanggal_perawatan', '>=', $tanggalMulai)
            ->get();
    }
}
