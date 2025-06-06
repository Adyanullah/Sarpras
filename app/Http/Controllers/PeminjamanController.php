<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\AjuanPeminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('ruangan')->get();
        $items = Peminjaman::with(['peminjamanItem.barang.ruangan', 'user'])->whereNot('status_ajuan', 'ditolak')->get();
        // dd($items);
        return view('peminjaman.app', compact('items', 'barangs'));
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal_peminjaman' => 'required|date',
                'tanggal_pengembalian' => 'required|date|after_or_equal:tanggal_peminjaman',
                'nama_peminjam' => 'required|string|max:255',
                'barang_id' => 'required|exists:barangs,id',
                'jumlah_barang' => 'required|integer|min:1',
                'status_peminjaman' => 'nullable|in:Dipinjam,Dikembalikan,Diperpanjang,Hilang',
                'keterangan' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'TambahPeminjaman'); // tanda modal mana yang error
        }

        $barang = Barang::findOrFail($validated['barang_id']);

        if ($validated['jumlah_barang'] > $barang->jumlah_barang) {
            return redirect()->back()
                ->withErrors(['jumlah_barang' => 'Jumlah barang yang diminta melebihi stok tersedia.'])
                ->withInput()
                ->with('modal_error', 'TambahPeminjaman');
        }

        $peminjaman = Peminjaman::create($validated);

        AjuanPeminjaman::create([
            'user_id' => Auth::id(),
            'peminjaman_id' => $peminjaman->id,
        ]);

        return redirect('/peminjaman')->with('success', 'Peminjaman berhasil diajukan.');
    }

    public function updateStatus(
        $id,
        $status,
        $jumlah_barang,
        $barang_id
    ) {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $allowedStatus = ['Dikembalikan', 'Hilang'];
        if (!in_array($status, $allowedStatus)) {
            return back()->with('error', 'Status tidak valid.');
        }
        $peminjaman->status_peminjaman = $status;
        $peminjaman->tanggal_pengembalian = now();
        $peminjaman->save();

        $barang = Barang::findOrFail($barang_id);
        if ($status == 'Dikembalikan') {
            $barang->jumlah_barang += $jumlah_barang;
        }
        $barang->save();

        return back()->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    public function laporan(Request $request)
    {
        // Ambil input filter dari query string
        $status = $request->input('status');
        $search = $request->input('search');

        // Query awal dengan relasi
        $query = Peminjaman::with(['barang.ruangan', 'ajuan']);

        // Filter status jika dipilih
        if ($status) {
            $query->where('status_peminjaman', $status);
        }

        // Filter pencarian jika diisi
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%");
                    });
            });
        }
        $items = $query->get();

        // $items = Peminjaman::with(['barang.ruangan', 'ajuan'])->get();
        return view('laporan.peminjaman.app', compact('items'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_peminjamanEdit' => 'required|date',
                'tanggal_pengembalianEdit' => 'required|date',
                'nama_peminjamEdit' => 'required|string|max:255',
                'barang_idEdit' => 'required|exists:barangs,id',
                'jumlah_barangEdit' => 'required|integer|min:1',
                'status_peminjamanEdit' => 'required|in:Dipinjam,Dikembalikan,Diperpanjang,Hilang',
                'keteranganEdit' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'editPeminjaman' . $id); // tandai modal edit yang error
        }

        $barang = Barang::findOrFail($validated['barang_id']);

        if ($validated['jumlah_barangEdit'] > $barang->jumlah_barang) {
            return redirect()->back()
                ->withErrors(['jumlah_barangEdit' => 'Jumlah barang yang diminta melebihi stok tersedia.'])
                ->withInput()
                ->with('modal_error', 'editPeminjaman' . $id);
        }

        if (strtotime($validated['tanggal_peminjamanEdit']) > strtotime($validated['tanggal_pengembalianEdit'])) {
        return redirect()->back()
            ->withErrors(['tanggal_peminjamanEdit' => 'Tanggal peminjaman tidak boleh lebih dari tanggal pengembalian.'])
            ->withInput()
            ->with('modal_error', 'editPeminjaman' . $id);
        }

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update($request->all());

        return redirect()->back()->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();
        return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function exportPDF($bulan)
    {
        $tanggalMulai = Carbon::now()->subMonths($bulan);

        $items = Peminjaman::with(['barang.ruangan', 'ajuan'])
            ->whereDate('tanggal_peminjaman', '>=', $tanggalMulai)
            ->get();

        $pdf = Pdf::loadView('laporan.peminjaman.pdf', compact('items'));
        return $pdf->download("laporan-peminjaman-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new PeminjamanExport($bulan), "laporan-peminjaman-{$bulan}-bulan.xlsx");
    }
}
