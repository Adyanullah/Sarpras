<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangRusakController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::whereIn('kondisi_barang', ['rusak', 'berat'])->where('sedia','>', '-1');

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tahun_perolehan', $request->tahun);
        }

        $dataInventaris = $query->distinct()->paginate(20)->withQueryString();

        $tahunList = Barang::selectRaw('YEAR(tahun_perolehan) as tahun')
            ->whereIn('kondisi_barang', ['rusak', 'berat'])
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('dashboard.barangRusak', compact('dataInventaris', 'tahunList'));
    }
}
