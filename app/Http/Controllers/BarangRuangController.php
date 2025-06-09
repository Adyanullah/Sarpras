<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;

class BarangRuangController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::withCount(['barangAktif'])->get();
        return view('laporan.barangruang.app', compact('ruangans'));
    }

    public function detail($id)
    {
        $ruangan = Ruangan::with('barangAktif.barangMaster')->findOrFail($id);
        return view('laporan.barangruang.detail', compact('ruangan'));
    }
    
}
