<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;

class BarangRuangController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::with('barang.barangMaster')->get();
        return view('laporan.barangruang.app', compact('ruangans'));
    }
}
