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
use Illuminate\Support\Facades\DB;

class PenghapusanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('ruangan')->get();
        $data = Penghapusan::with(['penghapusanItem.barang.ruangan', 'user'])->where('status_ajuan', 'pending')->get();
        return view('penghapusan.app', compact('data', 'barangs'));
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
        DB::transaction(function () use ($id) {
            $penghapusan = Penghapusan::findOrFail($id);

            // Ambil semua ID barang dari item yang terkait
            $barangIds = PenghapusanItem::where('penghapusan_id', $id)
                ->pluck('barang_id') // ambil langsung ID
                ->filter()           // buang null jika ada
                ->toArray();

            // Kembalikan status "sedia" ke 1 untuk semua barang terkait
            if (!empty($barangIds)) {
                Barang::whereIn('id', $barangIds)->update(['sedia' => 1]);
            }

            // Hapus penghapusan (otomatis hapus item jika relasi cascade di DB)
            $penghapusan->delete();
        });

        return redirect()->back()->with('success', 'Data penghapusan berhasil dibatalkan.');
    }
    
    // 1) Laporan dengan rentang tanggal
    public function laporan(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $search= $request->input('search');

        $query = PenghapusanItem::with(['barang.ruangan','barang.barangMaster','penghapusan'])
            ->whereHas('penghapusan', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('created_at','>=',$start)
                         ->whereDate('created_at','<=',$end)
                  );
            })
            ->when($search, fn($q) =>
                $q->whereHas('barang.barangMaster', fn($q2) =>
                    $q2->where('nama_barang','like', "%{$search}%")
                )
            );

        $data = $query->get();
        return view('laporan.penghapusan.app', compact('data'));
    }

    // 2) Export PDF
    public function exportPDF(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $data = PenghapusanItem::with(['barang.ruangan','barang.barangMaster','penghapusan'])
            ->whereHas('penghapusan', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('created_at','>=',$start)
                         ->whereDate('created_at','<=',$end)
                  );
            })->get();

        $pdf = Pdf::loadView('laporan.penghapusan.pdf', compact('data','start','end'));
        return $pdf->download("laporan-penghapusan-{$start}_{$end}.pdf");
    }

    // 3) Export Excel
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        return Excel::download(
            new \App\Exports\PenghapusanExport($start, $end),
            "laporan-penghapusan-{$start}_{$end}.xlsx"
        );
    }
}
