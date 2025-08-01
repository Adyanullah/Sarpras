<?php

namespace App\Http\Controllers;

use App\Models\AjuanMutasi;
use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MutasiExport;
use App\Models\MutasiItem;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['ruangan'])->get();
        $mutasi = Mutasi::with(['mutasiItem.barang.ruangan', 'user'])->where('status_ajuan', 'pending')->get();
        $ruangan = Ruangan::all();
        $ruangans = Ruangan::pluck('nama_ruangan', 'id')->toArray();

        return view('mutasi.app', compact('mutasi', 'ruangans', 'ruangan', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_mutasi'  => 'required|date',
                'nama_mutasi'     => 'required|string|max:255',
                'tujuan'          => 'required|integer',
                'keterangan'      => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            // Kirim modal id yang error ke session agar modal tersebut dibuka
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'editMutasi' . $id);
        }
        $mutasi = Mutasi::findOrFail($id);
        $mutasi->update($validated);

        return redirect()->back()->with('success', 'Data mutasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $mutasi = Mutasi::findOrFail($id);

            // Ambil semua ID barang dari item yang terkait
            $barangIds = MutasiItem::where('mutasi_id', $id)
                ->pluck('barang_id') // ambil langsung ID
                ->filter()           // buang null jika ada
                ->toArray();

            // Kembalikan status "sedia" ke 1 untuk semua barang terkait
            if (!empty($barangIds)) {
                Barang::whereIn('id', $barangIds)->update(['sedia' => 1]);
            }

            // Hapus mutasi (otomatis hapus item jika relasi cascade di DB)
            $mutasi->delete();
        });

        return redirect()->back()->with('success', 'Data peminjaman berhasil dibatalkan.');
    }
    
    // 1) Laporan avec rentang tanggal
    public function laporan(Request $request)
    {
        $start   = $request->input('start_date');
        $end     = $request->input('end_date');
        $search  = $request->input('search');
        $ruangans = Ruangan::pluck('nama_ruangan','id')->toArray();

        $query = MutasiItem::with(['barang.ruangan','barang.barangMaster','mutasi.user'])
            ->whereHas('mutasi', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('tanggal_mutasi','>=',$start)
                         ->whereDate('tanggal_mutasi','<=',$end)
                  );
            })
            // server-side search optional
            ->when($search, fn($q) =>
                $q->whereHas('barang.barangMaster', fn($q2) =>
                    $q2->where('nama_barang','like', "%{$search}%")
                       ->orWhere('kode_barang','like', "%{$search}%")
                )
            );

        $mutasi = $query->get();

        return view('laporan.mutasi.app', compact('mutasi','ruangans'));
    }

    // 2) Export PDF
    public function exportPDF(Request $request)
    {
        $start   = $request->input('start_date');
        $end     = $request->input('end_date');
        $ruangans = Ruangan::pluck('nama_ruangan','id')->toArray();

        $mutasi = MutasiItem::with(['barang.ruangan','barang.barangMaster','mutasi.user'])
            ->whereHas('mutasi', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('tanggal_mutasi','>=',$start)
                         ->whereDate('tanggal_mutasi','<=',$end)
                  );
            })->get();

        $pdf = Pdf::loadView('laporan.mutasi.pdf', compact('mutasi','ruangans','start','end'));
        return $pdf->download("laporan-mutasi-{$start}_{$end}.pdf");
    }

    // 3) Export Excel
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        return Excel::download(
            new \App\Exports\MutasiExport($start, $end),
            "laporan-mutasi-{$start}_{$end}.xlsx"
        );
    }
}
