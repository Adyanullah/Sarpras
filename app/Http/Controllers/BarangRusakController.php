<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangRusakController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::whereIn('kondisi_barang', ['rusak', 'berat']);

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_perolehan', $request->tahun);
        }

        // PAGINATION, jangan get()
        $dataInventaris = $query->paginate(20)->withQueryString();

        // Ambil daftar tahun perolehan untuk filter dropdown
        $tahunList = Barang::select('tahun_perolehan')
            ->whereIn('kondisi_barang', ['rusak', 'berat'])
            ->distinct()
            ->orderBy('tahun_perolehan', 'desc')
            ->pluck('tahun_perolehan');

        return view('dashboard.barangRusak', compact('dataInventaris', 'tahunList'));
    }
}
