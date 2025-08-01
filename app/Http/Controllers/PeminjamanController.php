<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PeminjamanItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('ruangan')->get();
        $items = Peminjaman::with(['peminjamanItem.barang.ruangan', 'user'])->whereNot('status_ajuan', 'ditolak')->where('status_peminjaman', 'Dipinjam')->get();
        return view('peminjaman.app', compact('items', 'barangs'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Dikembalikan,Hilang,Diperpanjang'
        ]);
        $peminjaman = Peminjaman::with('peminjamanItem.barang')->findOrFail($id);

        $allowedStatus = ['Dikembalikan', 'Hilang'];
        if (!in_array($validated['status'], $allowedStatus)) {
            return back()->with('error', 'Status tidak valid.');
        }
        $peminjaman->tanggal_pengembalian = now();
        $peminjaman->status_peminjaman = $validated['status'];
        $peminjaman->save();
        if ($validated['status'] === 'Hilang') {
            foreach ($peminjaman->peminjamanItem as $item) {
                $item->barang->sedia = -1;
                $item->barang->save();
            }
        }

        if ($validated['status'] === 'Dikembalikan') {
            $barangIds = $peminjaman->peminjamanItem->pluck('barang.id');
            Barang::whereIn('id', $barangIds)->update(['sedia' => 1]);
        }

        return back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_peminjaman' => 'required|date',
                'nama_peminjam' => 'required|string|max:255',
                'keterangan' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'editPeminjaman' . $id); // tandai modal edit yang error
        }

        // if (strtotime($validated['tanggal_peminjaman']) > strtotime($validated['tanggal_pengembalian'])) {
        //     return redirect()->back()
        //         ->withErrors(['tanggal_peminjaman' => 'Tanggal peminjaman tidak boleh lebih dari tanggal pengembalian.'])
        //         ->withInput()
        //         ->with('modal_error', 'editPeminjaman' . $id);
        // }

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update($request->all());

        return redirect()->back()->with('success', 'Data peminjaman berhasil diperbarui.');
    }
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::findOrFail($id);

            // Ambil semua ID barang dari item yang terkait
            $barangIds = PeminjamanItem::where('peminjaman_id', $id)
                ->pluck('barang_id') // ambil langsung ID
                ->filter()           // buang null jika ada
                ->toArray();

            // Kembalikan status "sedia" ke 1 untuk semua barang terkait
            if (!empty($barangIds)) {
                Barang::whereIn('id', $barangIds)->update(['sedia' => 1]);
            }

            // Hapus peminjaman (otomatis hapus item jika relasi cascade di DB)
            $peminjaman->delete();
        });

        return redirect()->back()->with('success', 'Data peminjaman berhasil dibatalkan.');
    }
    
    public function laporan(Request $request)
    {
        $start  = $request->input('start_date');
        $end    = $request->input('end_date');
        $search = $request->input('search');
        $status = $request->input('status');

        $query = PeminjamanItem::with(['barang.ruangan','barang.barangMaster','peminjaman.user'])
            ->whereHas('peminjaman', function($q) use($start,$end,$status) {
                $q->where('status_ajuan', 'disetujui')
                  ->when($status, fn($q2) => $q2->where('status_peminjaman', $status))
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('tanggal_peminjaman','>=',$start)
                         ->whereDate('tanggal_peminjaman','<=',$end)
                  );
            });

        if ($search) {
            $query->where(function($q) use($search) {
                $q->whereHas('peminjaman', fn($q2) =>
                       $q2->where('nama_peminjam','like', "%{$search}%")
                   )
                  ->orWhereHas('barang.barangMaster', fn($q2) =>
                       $q2->where('nama_barang','like', "%{$search}%")
                  );
            });
        }

        $items = $query->get();
        return view('laporan.peminjaman.app', compact('items'));
    }

    // 2) Export PDF
    public function exportPDF(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $items = PeminjamanItem::with(['barang.ruangan','barang.barangMaster','peminjaman.user'])
            ->whereHas('peminjaman', function($q) use($start,$end) {
                $q->where('status_ajuan','disetujui')
                  ->when($start && $end, fn($q2) =>
                      $q2->whereDate('tanggal_peminjaman','>=',$start)
                         ->whereDate('tanggal_peminjaman','<=',$end)
                  );
            })
            ->get();

        $pdf = Pdf::loadView('laporan.peminjaman.pdf', compact('items','start','end'));
        return $pdf->download("laporan-peminjaman-{$start}_{$end}.pdf");
    }

    // 3) Export Excel
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        return Excel::download(
            new PeminjamanExport($start, $end),
            "laporan-peminjaman-{$start}_{$end}.xlsx"
        );
    }
}
