<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangRusak;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil tahun sekarang sekali saja
        $tahunSekarang = now()->year;

        // 1) Total semua barang (sedia > -1)
        $totalBarang = Barang::where('sedia', '>', -1)->count();

        // 2) Total barang rusak/berat
        $barangRusak = Barang::whereIn('kondisi_barang', ['rusak', 'berat'])
            ->where('sedia', '>', -1)
            ->count();

        // 3) Barang masuk tahun ini (gunakan whereYear untuk kolom DATE)
        $barangMasukThisYear = Barang::whereYear('tahun_perolehan', $tahunSekarang)
            ->where('sedia', '>', -1)
            ->count();

        // 4) Hitung jumlah barang masuk per tahun
        $masukPerTahun = Barang::selectRaw('YEAR(tahun_perolehan) as tahun, COUNT(*) as total')
            ->where('sedia', '>', -1)
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->pluck('total', 'tahun');

        // 5) Hitung jumlah barang rusak per tahun (dari kolom created_at)
        $rusakPerTahun = BarangRusak::selectRaw('YEAR(created_at) as tahun, COUNT(*) as total')
            ->where('status_ajuan', 'disetujui')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->pluck('total', 'tahun');

        // 6) Buat label tahun unik (gabungan masuk + rusak)
        $tahunLabels = $masukPerTahun->keys()
            ->merge($rusakPerTahun->keys())
            ->unique()
            ->sort()
            ->values();

        // 7) Susun array data untuk chart
        $dataBarangMasuk = $tahunLabels->map(fn($t) => $masukPerTahun[$t] ?? 0)->toArray();
        $dataBarangRusak = $tahunLabels->map(fn($t) => $rusakPerTahun[$t] ?? 0)->toArray();

        return view('dashboard.app', compact(
            'totalBarang',
            'barangRusak',
            'barangMasukThisYear',
            'tahunSekarang',
            'tahunLabels',
            'dataBarangMasuk',
            'dataBarangRusak'
        ));
    }
}
