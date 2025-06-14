<?php

namespace App\Http\Controllers;

use App\Models\AjuanPerawatan;
use App\Models\Barang;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

            $barangIds = $perawatan->perawatanItem->pluck('barang_id');
            Barang::whereIn('id', $barangIds)->update([
                'kondisi_barang' => $validated['kondisi_barang'],
                'sedia' => 1
            ]);

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
        $perawatan = Perawatan::findOrFail($id);
        $perawatan->delete();

        return redirect()->back()->with('success', 'Data perawatan berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        $search = $request->input('search');
        $query = PerawatanItem::with('barang.ruangan', 'perawatan.user', 'barang.barangMaster')->whereHas('perawatan', function ($q) {
            $q->where('status_ajuan', 'disetujui');
        });
        // $query = PerawatanItem::all();
        // dd($query->get());

        // Fitur pencarian berdasarkan nama barang
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('barang.barangMaster', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%');
            });
        }

        $dataPerawatan = $query->get();
        // $dataPerawatan = Perawatan::with('barang.ruangan', 'ajuan')->get();
        return view('laporan.perawatan.app', compact('dataPerawatan'));
    }

    public function exportPDF($bulan)
    {
        $tanggalMulai = Carbon::now()->subMonths($bulan);
        // $dataPerawatan = Perawatan::with('perawatanItem.barang.ruangan', 'user')
        //     ->where('tanggal_perawatan', '>=', $tanggalMulai)
        //     ->get();
        $dataPerawatan = PerawatanItem::with('barang.ruangan', 'perawatan.user', 'barang.barangMaster')->whereHas('perawatan', function ($q) use ($tanggalMulai) {
            $q->where('status_ajuan', 'disetujui')
            ->where('tanggal_perawatan', '>=', $tanggalMulai);
        })->get();

        $pdf = Pdf::loadView('laporan.perawatan.pdf', compact('dataPerawatan'));
        return $pdf->download("laporan-perawatan-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new PerawatanExport($bulan), "laporan-perawatan-{$bulan}-bulan.xlsx");
    }
}
