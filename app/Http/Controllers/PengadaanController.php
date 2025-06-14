<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengadaan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PengadaanExport;
use App\Models\BarangMaster;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = Pengadaan::with('user', 'ruangan', 'barangMaster')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $barangs = Barang::with('ruangan')->get();
        $master = BarangMaster::with('barang')->get();
        $ruangans = Ruangan::all();
        return view('pengadaan.app', compact('pengadaans', 'barangs', 'ruangans', 'master'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pengajuan' => 'required|in:tambah,baru',
            'jumlah' => 'required|integer|min:1',
            'ruangan_id' => 'required|exists:ruangans,id',
            'sumber_dana' => 'required|string|max:255',
            'harga_perolehan' => 'required|numeric|min:0',
            'cv_pengadaan' => 'required|string|max:255',
        ]);
        $data = [
            'user_id' => Auth::id(),
            'status' => 'pending',
            'jumlah' => $request->jumlah,
            'tipe_pengajuan' => $request->tipe_pengajuan,
            'ruangan_id' => $request->ruangan_id,
            'sumber_dana' => $request->sumber_dana,
            'harga_perolehan' => $request->harga_perolehan,
            'cv_pengadaan' => $request->cv_pengadaan,
            'tahun_perolehan' => $request->tahun_perolehan,
            'keterangan' => $request->keterangan,
            'kondisi_barang' => 'baik',
        ];

        if ($request->tipe_pengajuan === 'tambah') {
            // Pengadaan barang lama
            $request->validate([
                'barang_id' => 'required|exists:barang_masters,id',
            ]);
            $data['barang_master_id'] = $request->barang_id;
        } elseif ($request->tipe_pengajuan === 'baru') {
            // Pengadaan barang baru
            $request->validate([
                'kode_barang' => 'required|string|unique:barang_masters,kode_barang',
                'nama_barang' => 'required|string|max:255',
            ]);
            $data['kode_barang'] = $request->kode_barang;
            $data['nama_barang'] = $request->nama_barang;
            $data['jenis_barang'] = $request->jenis_barang;
            $data['merk_barang'] = $request->merk_barang;
            // Upload gambar jika ada
            if ($request->hasFile('gambar_barang')) {
                $file = $request->file('gambar_barang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = public_path('uploads/inventaris');

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $file->move($path, $filename);
                $data['gambar_barang'] = 'uploads/inventaris/' . $filename;
            }
        }

        Pengadaan::create($data);

        return redirect()->back()->with('success', 'Pengajuan pengadaan berhasil diajukan dan menunggu persetujuan.');
    }

    public function update(Request $request, $id)
    {
        $pengadaan = Pengadaan::findOrFail($id);

        $rules = [
            'jumlah' => 'required|integer|min:1',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($pengadaan->tipe_pengajuan === 'baru') {
            $rules += [
                'nama_barang' => 'required|string|max:255',
                'jenis_barang' => 'required|string|max:255',
                'merk_barang' => 'required|string|max:255',
                'ruangan_id' => 'required|exists:ruangans,id',
                'kondisi_barang' => 'required|in:baik,rusak,berat',
            ];
        } else {
            $rules['barang_id'] = 'required|exists:barangs,id';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('gambar_barang')) {
            $file = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/inventaris');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $data['gambar_barang'] = 'uploads/inventaris/' . $filename;
        }

        $pengadaan->update($data);

        return redirect()->back()->with('success', 'Data pengadaan berhasil diperbarui.');
    }

    public function laporan(Request $request)
    {
        $search = $request->input('search');
        $tahun = $request->input('tahun');

        $pengadaans = Pengadaan::with('barangMaster')
            ->where('status', 'disetujui')
            ->when($search, function ($query, $search) {
                $query->whereHas('barangMaster', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            })
            ->when($tahun, function ($query, $tahun) {
                $query->whereYear('tanggal_pengadaan', $tahun);
            })
            ->get();

        return view('laporan.pengadaan.app', compact('pengadaans'));
    }


    public function exportPDF($bulan)
    {
        $tanggalMulai = Carbon::now()->subMonths($bulan);

        $pengadaans = Pengadaan::whereDate('created_at', '>=', $tanggalMulai)
            ->where('status', 'disetujui')->get();

        $pdf = Pdf::loadView('laporan.pengadaan.pdf', compact('pengadaans'));
        return $pdf->download("laporan-pengadaan-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new PengadaanExport($bulan), "laporan-pengadaan-{$bulan}-bulan.xlsx");
    }
}
