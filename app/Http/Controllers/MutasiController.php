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

    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'tanggal_mutasi'  => 'required|date',
    //             'nama_mutasi'     => 'required|string|max:255',
    //             'barang_id'       => 'required|exists:barangs,id',
    //             'jumlah_barang'   => 'required|integer|min:1',
    //             'tujuan'          => 'required|integer|exists:ruangans,id',
    //             'keterangan'      => 'nullable|string',
    //         ]);
    //     } catch (ValidationException $e) {
    //         return redirect()->back()
    //             ->withErrors($e->validator)
    //             ->withInput()
    //             ->with('modal_error', 'modalMutasiBarang');
    //     }

    //     $barangAsal = Barang::findOrFail($validated['barang_id']);

    //     // Cek jika ruangan tujuan sama dengan asal
    //     if ($validated['tujuan'] == $barangAsal->ruangan_id) {
    //         return redirect()->back()
    //             ->withErrors(['tujuan' => 'Ruangan tujuan tidak boleh sama dengan ruangan asal.'])
    //             ->withInput()
    //             ->with('modal_error', 'modalMutasiBarang');
    //     }

    //     // Validasi stok
    //     if ($validated['jumlah_barang'] > $barangAsal->jumlah_barang) {
    //         return redirect()->back()
    //             ->withErrors(['jumlah_barang' => 'Jumlah melebihi stok yang tersedia.'])
    //             ->withInput()
    //             ->with('modal_error', 'modalMutasiBarang');
    //     }

    //     $mutasi = Mutasi::create($validated);

    //     AjuanMutasi::create([
    //         'user_id' => Auth::id(),
    //         'mutasi_id' => $mutasi->id,
    //     ]);

    //     return redirect()->back()->with('success', 'Data mutasi berhasil disimpan.');
    // }

    public function laporan(Request $request)
    {
        $search = $request->input('search');
        $ruangans = Ruangan::pluck('nama_ruangan', 'id')->toArray();

        $mutasi = MutasiItem::with(['barang.ruangan', 'mutasi.user', 'barang.barangMaster'])
            ->whereHas('mutasi', function ($q) {
                $q->where('status_ajuan', 'disetujui');
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('barang.barangMaster', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                    });
                });
            })
            ->get();

        return view('laporan.mutasi.app', compact('mutasi', 'ruangans'));
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

    public function exportPDF($bulan)
    {
        $tanggalMulai = Carbon::now()->subMonths($bulan);

        $mutasi = Mutasi::with(['barang.ruangan', 'ajuan'])
            ->whereDate('tanggal_mutasi', '>=', $tanggalMulai)
            ->get();

        $ruangans = Ruangan::pluck('nama_ruangan', 'id')->toArray();

        $pdf = Pdf::loadView('laporan.mutasi.pdf', compact('mutasi', 'ruangans'));
        return $pdf->download("laporan-mutasi-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new MutasiExport($bulan), "laporan-mutasi-{$bulan}-bulan.xlsx");
    }
}
