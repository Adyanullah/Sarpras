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
            // 'tipe_pengajuan' => 'required|in:tambah,baru',
            // 'jumlah' => 'required|integer|min:1',
            // 'ruangan_id' => 'required|exists:ruangans,id',
            // 'sumber_dana' => 'required|in:BOS,DAK,Hibah',
            // 'harga_perolehan' => 'required|numeric|min:0',
            // 'cv_pengadaan' => 'required|string|max:255',
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
            'kepemilikan_barang' => $request->kepemilikan_barang,
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
    // public function createTambahJumlah()
    // {
    //     $barangs = Barang::all();
    //     return view('barang_requests.tambah_jumlah', compact('barangs'));
    // }

    // public function createTambahBaru()
    // {
    //     $ruangans = Ruangan::all();
    //     return view('barang_requests.tambah_baru', compact('ruangans'));
    // }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'tipe_pengajuan' => 'required|in:baru,tambah',
    //         'nama_barang' => 'required_if:tipe_pengajuan,baru|string|max:255',
    //         'jenis_barang' => 'required_if:tipe_pengajuan,baru|string|max:255',
    //         'merk_barang' => 'required_if:tipe_pengajuan,baru|string|max:255',
    //         'tahun_perolehan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
    //         'sumber_dana' => 'required_if:tipe_pengajuan,baru|in:bos,dak,hibah',
    //         'harga_perolehan' => 'nullable|numeric|min:0',
    //         'cv_pengadaan' => 'nullable|string|max:255',
    //         'jumlah' => 'required|integer|min:1',
    //         'ruangan_id' => 'required_if:tipe_pengajuan,baru|exists:ruangans,id',
    //         'kondisi_barang' => 'nullable|string|in:baik,rusak,berat',
    //         'kepemilikan_barang' => 'required_if:tipe_pengajuan,baru|string|max:255',
    //         'penanggung_jawab' => 'nullable|string|max:255',
    //         'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         'barang_id' => 'required_if:tipe_pengajuan,tambah|exists:barangs,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $data = $validator->validated();
    //     $data['user_id'] = Auth::id();
    //     $data['status'] = 'pending';

    //     if ($request->hasFile('gambar_barang')) {
    //         $file = $request->file('gambar_barang');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $path = public_path('uploads/inventaris');

    //         if (!file_exists($path)) {
    //             mkdir($path, 0777, true);
    //         }

    //         $file->move($path, $filename);
    //         $data['gambar_barang'] = 'uploads/inventaris/' . $filename;
    //     }

    //     if ($data['tipe_pengajuan'] === 'tambah') {
    //         // Untuk pengajuan penambahan jumlah barang
    //         $barang = Barang::findOrFail($data['barang_id']);

    //         $data['kode_barang'] = $barang->kode_barang;
    //         $data['nama_barang'] = $barang->nama_barang;
    //         $data['jenis_barang'] = $barang->jenis_barang;
    //         $data['merk_barang'] = $barang->merk_barang;
    //         $data['tahun_perolehan'] = $barang->tahun_perolehan;
    //         $data['sumber_dana'] = $barang->sumber_dana;
    //         $data['harga_perolehan'] = $barang->harga_perolehan;
    //         $data['cv_pengadaan'] = $barang->cv_pengadaan;
    //         $data['ruangan_id'] = $barang->ruangan_id;
    //         $data['kondisi_barang'] = $barang->kondisi_barang;
    //         $data['kepemilikan_barang'] = $barang->kepemilikan_barang;
    //         $data['penanggung_jawab'] = $barang->penanggung_jawab;
    //         $data['gambar_barang'] = $barang->gambar_barang;
    //     } else {
    //         $data['kode_barang'] = 'BRG-' . strtoupper(Str::random(6)); // Otomatis generate kode
    //     }

    //     Pengadaan::create($data);

    //     return redirect()->back()->with('success', 'Pengajuan berhasil dikirim dan menunggu persetujuan.');
    // }

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

        $pengadaans = Pengadaan::whereDate('created_at', '>=', $tanggalMulai)->get();

        $pdf = Pdf::loadView('laporan.pengadaan.pdf', compact('pengadaans'));
        return $pdf->download("laporan-pengadaan-{$bulan}-bulan.pdf");
    }

    public function exportExcel($bulan)
    {
        return Excel::download(new PengadaanExport($bulan), "laporan-pengadaan-{$bulan}-bulan.xlsx");
    }
}
