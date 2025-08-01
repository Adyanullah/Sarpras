<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\PerawatanExport;
use App\Models\PerawatanItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PerawatanController extends Controller
{
    public function index()
    {
        $barang = Barang::with('ruangan')->get();
        $dataPerawatan = Perawatan::with('perawatanItem.barang.ruangan', 'user')->where('status_ajuan', '!=', 'ditolak')->where('status_perawatan', 'belum')->get();
        return view('perawatan.app', compact('dataPerawatan', 'barang'));
    }

    public function UpdateStatus(Request $request, $id)
    {
        $perawatan = Perawatan::with('perawatanItem.barang')->find($id);

        $validated = $request->validate([
            'kondisi_barang' => 'required|in:baik,rusak,berat',
            'status' => 'nullable|in:selesai,belum',
        ]);

        if ($perawatan) {
            $perawatan->status_perawatan = $validated['status'];
            $perawatan->tanggal_selesai = now();
            $perawatan->save();
        foreach ($perawatan->perawatanItem as $item) {
            $barang = $item->barang;
            if($barang){
                $barang->kondisi_barang = $validated['kondisi_barang'];
                $barang->sedia = 1;
                $barang->save();
            }
        }
            // $barangIds = $perawatan->perawatanItem->pluck('barang_id');
            // Barang::whereIn('id', $barangIds)->update([
            //     'kondisi_barang' => $validated['kondisi_barang'],
            //     'sedia' => 1
            // ]);

            return redirect()->back()->with('success', 'Status dan kondisi barang berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Perawatan tidak ditemukan.');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_perawatan' => 'required|date',
                'jenis_perawatan'   => 'required|string',
                'biaya_perawatan'   => 'nullable|integer|min:0',
                'keterangan'        => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'editPerawatan' . $id);
        }

        $perawatan = Perawatan::findOrFail($id);
        $perawatan->update($validated);

        return redirect()->back()->with('success', 'Data perawatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $perawatan = Perawatan::findOrFail($id);

            // Ambil semua ID barang dari item yang terkait
            $barangIds = PerawatanItem::where('perawatan_id', $id)
                ->pluck('barang_id') // ambil langsung ID
                ->filter()           // buang null jika ada
                ->toArray();

            // Kembalikan status "sedia" ke 1 untuk semua barang terkait
            if (!empty($barangIds)) {
                Barang::whereIn('id', $barangIds)->update(['sedia' => 1]);
            }

            // Hapus perawatan (otomatis hapus item jika relasi cascade di DB)
            $perawatan->delete();
        });
        return redirect()->back()->with('success', 'Data perawatan berhasil dibatalkan.');
    }

    public function laporan(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $search = $request->input('search');

        $query = PerawatanItem::with('barang.ruangan','perawatan.user','barang.barangMaster')
            ->whereHas('perawatan', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                ->when($start && $end, fn($q2) =>
                    $q2->whereDate('tanggal_perawatan','>=',$start)
                        ->whereDate('tanggal_perawatan','<=',$end)
                );
            });

        // server-side search (optional)
        if ($search) {
            $query->whereHas('barang.barangMaster', fn($q2) =>
                $q2->where('nama_barang','like', "%{$search}%")
            );
        }

        $dataPerawatan = $query->get();

        return view('laporan.perawatan.app', compact('dataPerawatan'));
    }

    // 2) EXPORT PDF
    public function exportPDF(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $dataPerawatan = PerawatanItem::with('barang.ruangan','perawatan.user','barang.barangMaster')
            ->whereHas('perawatan', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                ->when($start && $end, fn($q2) =>
                    $q2->whereDate('tanggal_perawatan','>=',$start)
                        ->whereDate('tanggal_perawatan','<=',$end)
                );
            })
            ->get();

        $pdf = Pdf::loadView('laporan.perawatan.pdf', compact('dataPerawatan','start','end'));
        return $pdf->download("laporan-perawatan-{$start}_{$end}.pdf");
    }

    // 3) EXPORT EXCEL
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        return Excel::download(
            new PerawatanExport($start, $end),
            "laporan-perawatan-{$start}_{$end}.xlsx"
        );
    }
}
