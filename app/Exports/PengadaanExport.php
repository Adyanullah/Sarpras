<?php

namespace App\Exports;

use App\Models\Pengadaan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PengadaanExport implements FromCollection
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);

        return Pengadaan::whereDate('created_at', '>=', $tanggalMulai)->get();
    }
}