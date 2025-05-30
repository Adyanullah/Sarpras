<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PeminjamanExport implements FromCollection
{
    protected $bulan;

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $tanggalMulai = Carbon::now()->subMonths($this->bulan);
        return Peminjaman::with(['barang.ruangan', 'ajuan'])
            ->whereDate('tanggal_peminjaman', '>=', $tanggalMulai)
            ->get();
    }
}
