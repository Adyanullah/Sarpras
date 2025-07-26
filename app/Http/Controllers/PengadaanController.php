<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengadaan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PengadaanExport;
use App\Models\BarangMaster;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = Pengadaan::with(['user','barangMaster','items.ruangan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $barangs = Barang::with('ruangan')->get();
        $master = BarangMaster::with('barang')->get();
        $ruangans = Ruangan::all();

        return view('pengadaan.app', compact(
            'pengadaans', 'barangs', 'ruangans', 'master'
        ));
    }

    public function store(Request $request)
    {
        // 1) Validasi master + array
        $rules = [
            'tipe_pengajuan'   => 'required|in:tambah,baru',
            'sumber_dana'      => 'required|string',
            'harga_perolehan'  => 'required|numeric|min:0',
            'cv_pengadaan'     => 'required|string',
            'tahun_perolehan'  => 'nullable|digits:4',
            'keterangan'       => 'nullable|string',
            'ruangan_id'       => 'required|array|min:1',
            'ruangan_id.*'     => 'exists:ruangans,id',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'integer|min:1',
        ];

        if ($request->tipe_pengajuan === 'tambah') {
            $rules['barang_id'] = 'required|exists:barang_masters,id';
        } else {
            // untuk pengajuan barang baru, tambahkan validasi jenis & merk
            $rules['kode_barang']  = 'required|string|unique:barang_masters,kode_barang';
            $rules['nama_barang']  = 'required|string|max:255';
            $rules['jenis_barang'] = 'required|string|max:255';
            $rules['merk_barang']  = 'required|string|max:255';
            $rules['gambar_barang']= 'nullable|image';
        }

        $v = $request->validate($rules);

        // 2) Siapkan data master Pengadaan (tanpa lokasi/jumlah)
        $masterData = [
            'user_id'         => Auth::id(),
            'status'          => 'pending',
            'tipe_pengajuan'  => $v['tipe_pengajuan'],
            'sumber_dana'     => $v['sumber_dana'],
            'harga_perolehan' => $v['harga_perolehan'],
            'cv_pengadaan'    => $v['cv_pengadaan'],
            'tahun_perolehan' => $v['tahun_perolehan'] ?? now()->year,
            'keterangan'      => $v['keterangan'] ?? null,
            'kondisi_barang'  => 'baik',
        ];

        if ($v['tipe_pengajuan'] === 'tambah') {
            // simpan referensi master barang yang ada
            $masterData['barang_master_id'] = $v['barang_id'];
        } else {
            // simpan data barang baru
            $masterData['kode_barang']   = $v['kode_barang'];
            $masterData['nama_barang']   = $v['nama_barang'];
            $masterData['jenis_barang']  = $v['jenis_barang'];
            $masterData['merk_barang']   = $v['merk_barang'];
            // upload gambar jika ada
            if ($request->hasFile('gambar_barang')) {
                $file     = $request->file('gambar_barang');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/inventaris'), $filename);
                $masterData['gambar_barang'] = 'uploads/inventaris/'.$filename;
            }
        }

        // 3) Buat master request
        $pengadaan = Pengadaan::create($masterData);

        // 4) Loop untuk tiap lokasi/jumlah, simpan di pengadaan_items
        foreach ($v['ruangan_id'] as $i => $rid) {
            $pengadaan->items()->create([
                'ruangan_id' => $rid,
                'jumlah'     => $v['jumlah'][$i],
            ]);
        }

        return redirect()->back()
            ->with('success', 'Pengajuan berhasil dibuat untuk '.count($v['ruangan_id']).' lokasi.');
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
            $path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/inventaris';

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
