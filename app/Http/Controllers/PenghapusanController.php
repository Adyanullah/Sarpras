<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penghapusan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PenghapusanExport;
use App\Models\PenghapusanItem;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PenghapusanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('ruangan')->get();
        $data = Penghapusan::with(['penghapusanItem.barang.ruangan', 'user'])->where('status_ajuan', 'pending')->get();
        return view('penghapusan.app', compact('data', 'barangs'));
    }

    public function laporan(Request $request)
    {
        $search = $request->input('search');
        $query = PenghapusanItem::with(['barang.ruangan', 'penghapusan', 'barang.barangMaster']);

        // Filter berdasarkan pencarian barang
        if ($search) {
            $query->whereHas('barang.barangMaster', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%');
            });
        }

        $data = $query->get();
        // $data = Penghapusan::with(['barang.ruangan', 'ajuan'])->get();
        return view('laporan.penghapusan.app', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string',
        ]);

        $penghapusan = Penghapusan::findOrFail($id);
        $penghapusan->keterangan = $request->keterangan;
        $penghapusan->save();

        // return response()->json(['message' => 'Data penghapusan berhasil diupdate', 'data' => $penghapusan]);
        return redirect()->back()->with('success', 'Berhasil diupdate.');
    }

    // Method untuk menghapus data penghapusan
    public function destroy($id)
    {
        $penghapusan = Penghapusan::findOrFail($id);
        $penghapusan->delete();

        return redirect()->back()->with('success', 'Penghapusan berhasil dibatalkan.');
    }

    public function exportPDF($bulan)
    {
        $tanggalMulai = Carbon::now()->subMonths($bulan);

        $data = Penghapusan::with(['penghapusanItem.barang.ruangan', 'user'])
            // ->whereDate('created_at', '>=', $tanggalMulai)
            // ->whereHas('ajuan', function ($q) {
            //     $q->where('status', 'pending');
            // })
            ->get();

        $pdf = Pdf::loadView('laporan.penghapusan.pdf', compact('data'));
        return $pdf->download("laporan-penghapusan-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new PenghapusanExport($bulan), "laporan-penghapusan-{$bulan}-bulan.xlsx");
    }
}
