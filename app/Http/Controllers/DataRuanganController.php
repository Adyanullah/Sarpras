<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataRuanganController extends Controller
{
    public function index()
    {

        $dataRuang = Ruangan::withCount('barangAktif')->get();
        return view('pengaturan.ruang', compact('dataRuang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string',
        ]);
        $validated['kode_ruangan'] = 'R-' . strtoupper(Str::random(6));
        Ruangan::create($validated);
        return redirect('/ruang');
    }

    public function edit(Request $request,$id){
        $validated = $request->validate([
            'nama_ruangan' => 'required|string',
        ]);
        // Update data
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($validated);
        return redirect('/ruang');
    }

    public function destroy($id)
    {
        $dataRuang = Ruangan::findOrFail($id);
        $dataRuang->delete();
        return redirect('/ruang');
    }
}
