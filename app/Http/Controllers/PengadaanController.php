<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengadaan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PengadaanExport;
use App\Imports\PengadaanExistingImport;
use App\Models\BarangMaster;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = Pengadaan::with(['user', 'barangMaster', 'items.ruangan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $barangs = Barang::with('ruangan')->get();
        $master = BarangMaster::with('barang')->get();
        $ruangans = Ruangan::all();

        return view('pengadaan.app', compact(
            'pengadaans',
            'barangs',
            'ruangans',
            'master'
        ));
    }

    public function store(Request $request)
{
    // 1) Validasi master + array
    $rules = [
        'tipe_pengajuan'   => 'required|in:tambah,baru',
        'sumber_dana'      => 'required|string',
        'harga_perolehan'  => 'required|numeric|min:0',
        'cv_pengadaan'     => 'nullable|string',
        // ganti validasi digits:4 jadi date
        'tahun_perolehan'  => 'nullable|date|before_or_equal:today',
        'keterangan'       => 'nullable|string',
        'ruangan_id'       => 'required|array|min:1',
        'ruangan_id.*'     => 'exists:ruangans,id',
        'jumlah'           => 'required|array|min:1',
        'jumlah.*'         => 'integer|min:1',
    ];

    if ($request->tipe_pengajuan === 'tambah') {
        $rules['barang_id'] = 'required|exists:barang_masters,id';
    } else {
        $rules['kode_barang'] = [
            'required',
            'string',
            Rule::unique('barang_masters', 'kode_barang'),
            Rule::unique('pengadaans', 'kode_barang')
                ->where(fn($q) => $q->where('status', 'pending')),
        ];
        $rules['nama_barang']   = 'required|string|max:255';
        $rules['jenis_barang']  = 'required|string|max:255';
        $rules['merk_barang']   = 'required|string|max:255';
        $rules['gambar_barang'] = 'nullable|image';
    }

    $messages = [
        'kode_barang.unique' => 'Kode barang sudah ada atau masih dalam pengajuan pending.',
    ];

    $v = $request->validate($rules, $messages);

    // 2) Siapkan data master Pengadaan (tanpa lokasi/jumlah)
    $masterData = [
        'user_id'         => Auth::id(),
        'status'          => 'pending',
        'tipe_pengajuan'  => $v['tipe_pengajuan'],
        'sumber_dana'     => $v['sumber_dana'],
        'harga_perolehan' => $v['harga_perolehan'],
        'cv_pengadaan'    => $v['cv_pengadaan'] ?? null,
        // langsung simpan YYYY-MM-DD, atau default hari ini
        'tahun_perolehan' => $v['tahun_perolehan'] ?? now()->toDateString(),
        'keterangan'      => $v['keterangan'] ?? null,
        'kondisi_barang'  => 'baik',
    ];

    if ($v['tipe_pengajuan'] === 'tambah') {
        $masterData['barang_master_id'] = $v['barang_id'];
    } else {
        $masterData['kode_barang']  = $v['kode_barang'];
        $masterData['nama_barang']  = $v['nama_barang'];
        $masterData['jenis_barang'] = $v['jenis_barang'];
        $masterData['merk_barang']  = $v['merk_barang'];

        if ($request->hasFile('gambar_barang')) {
            $file     = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/inventaris'), $filename);
            $masterData['gambar_barang'] = 'uploads/inventaris/' . $filename;
        }
    }

    // 3) Buat master request
    $pengadaan = Pengadaan::create($masterData);

    // 4) Loop untuk tiap lokasi/jumlah
    foreach ($v['ruangan_id'] as $i => $rid) {
        $pengadaan->items()->create([
            'ruangan_id' => $rid,
            'jumlah'     => $v['jumlah'][$i],
        ]);
    }

    return redirect()->back()
        ->with('success', 'Pengajuan berhasil dibuat untuk ' . count($v['ruangan_id']) . ' lokasi.');
}

    public function importExisting(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt',
        ]);

        $import = new PengadaanExistingImport();

        // lakukan import
        Excel::import($import, $request->file('file'));

        // ambil pesan error
        $errors = $import->getErrors();

        if (count($errors)) {
            // simpan ke session agar bisa ditampilkan di view
            return back()
                ->with('import_errors', $errors)
                ->with('success', 'Import selesai dengan beberapa peringatan.');
        }

        return back()->with('success', 'Import pengadaan berhasil tanpa error.');
    }

    public function update(Request $request, $id)
    {
        $pengadaan = Pengadaan::with('items')->findOrFail($id);
        // 1) Validasi
        $rules = [
            'tipe_pengajuan'   => 'required|in:tambah,baru',
            'sumber_dana'      => 'required|string|max:255',
            'harga_perolehan'  => 'required|numeric|min:0',
            'cv_pengadaan'     => 'required|string|max:255',
            'tahun_perolehan'  => 'nullable|date|before_or_equal:today',
            'keterangan'       => 'nullable|string',
            'ruangan_id'       => 'required|array|min:1',
            'ruangan_id.*'     => 'exists:ruangans,id',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'integer|min:1',
            'gambar_barang'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($pengadaan->tipe_pengajuan === 'tambah') {
            $rules['barang_id'] = 'required|exists:barang_masters,id';
        } else {
            // Tambahkan validasi kode_barang
            $rules['kode_barang'] = [
                'required',
                'string',
                'max:255',
                // unik di barang_masters
                Rule::unique('barang_masters', 'kode_barang'),
                // unik di pengadaans pending, kecuali diri sendiri
                // Rule::unique('pengadaans', 'kode_barang')
                //     ->where('status', 'pending')
                //     ->ignore($pengadaan->id),
            ];
            $rules['nama_barang']  = 'required|string|max:255';
            $rules['jenis_barang'] = 'required|string|max:255';
            $rules['merk_barang']  = 'required|string|max:255';
        }

        $v = $request->validate($rules);

        // 2) Update master
        $data = [
            'sumber_dana'     => $v['sumber_dana'],
            'harga_perolehan' => $v['harga_perolehan'],
            'cv_pengadaan'    => $v['cv_pengadaan'],
            'tahun_perolehan' => $v['tahun_perolehan'] ?? $pengadaan->tahun_perolehan,
            'keterangan'      => $v['keterangan'] ?? $pengadaan->keterangan,
        ];

        if ($pengadaan->tipe_pengajuan === 'tambah') {
            $data['barang_master_id'] = $v['barang_id'];
        } else {
            $data['kode_barang']   = $v['kode_barang'];
            $data['nama_barang']  = $v['nama_barang'];
            $data['jenis_barang'] = $v['jenis_barang'];
            $data['merk_barang']  = $v['merk_barang'];
        }

        // 3) Upload gambar (jika ada)
        if ($request->hasFile('gambar_barang')) {
            if ($pengadaan->gambar_barang && file_exists(public_path($pengadaan->gambar_barang))) {
                unlink(public_path($pengadaan->gambar_barang));
            }
            $file     = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/inventaris'), $filename);
            $data['gambar_barang'] = 'uploads/inventaris/' . $filename;
        }

        $pengadaan->update($data);

        // 4) Re‐create detail items
        $pengadaan->items()->delete();
        foreach ($v['ruangan_id'] as $i => $rid) {
            $pengadaan->items()->create([
                'ruangan_id' => $rid,
                'jumlah'     => $v['jumlah'][$i],
            ]);
        }

        return back()->with('success', 'Data pengadaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengadaan = Pengadaan::findOrFail($id);
        // Hapus semua itemnya dulu (karena ada cascade, ini opsional)
        $pengadaan->items()->delete();
        $pengadaan->delete();

        return redirect()
            ->route('pengadaan.app')
            ->with('success', 'Pengajuan pengadaan berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        $search    = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $query = Pengadaan::with(['barangMaster', 'items'])
            ->where('status', 'disetujui')
            // server-side search tetap optional
            ->when($search, fn($q) => $q->whereHas(
                'barangMaster',
                fn($qb) =>
                $qb->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
            ))
            // filter tanggal jika keduanya ada
            ->when(
                $startDate && $endDate,
                fn($q) =>
                $q->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
            );

        $pengadaans = $query->get()
            ->map(fn($p) => tap($p, function ($x) {
                $x->jumlah_total = $x->items->sum('jumlah');
                $x->total_harga  = $x->jumlah_total * $x->harga_perolehan;
            }));

        return view('laporan.pengadaan.app', compact('pengadaans'));
    }


    public function exportPDF(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Pengadaan::with(['barangMaster', 'items'])
            ->where('status', 'disetujui')
            ->when(
                $start && $end,
                fn($q) =>
                $q->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end)
            );

        $pengadaans = $query->get()->map(fn($p) => tap($p, function ($x) {
            $x->jumlah_total = $x->items->sum('jumlah');
            $x->total_harga  = $x->jumlah_total * $x->harga_perolehan;
        }));

        $pdf = Pdf::loadView('laporan.pengadaan.pdf', compact('pengadaans', 'start', 'end'));
        return $pdf->download("laporan-pengadaan-{$start}-{$end}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        return Excel::download(
            new PengadaanExport($start, $end),
            "laporan-pengadaan-{$start}-{$end}.xlsx"
        );
    }
}
