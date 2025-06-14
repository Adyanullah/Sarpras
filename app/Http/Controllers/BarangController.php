<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMaster;
use App\Models\BarangRusak;
use App\Models\Mutasi;
use App\Models\MutasiItem;
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Penghapusan;
use App\Models\PenghapusanItem;
use App\Models\Perawatan;
use App\Models\PerawatanItem;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{

    public function index(Request $request)
    {
        $ruangan = Ruangan::all();
        $barangs = Barang::with('ruangan', 'barangMaster')
            ->select('barang_id', DB::raw('COUNT(*) as total_unit'))
            ->groupBy('barang_id')
            ->where('sedia', 1)
            ->paginate(8)
            ->appends($request->query());
        $ajuan = Barang::select('barang_id', DB::raw('COUNT(*) as total_ajuan'))
            ->where('sedia', '>', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id'); // <-- agar bisa diakses dengan [barang_id]
        $barangDetails = BarangMaster::whereIn('id', $barangs->pluck('barang_id'))
            ->get()
            ->keyBy('id');
        return view('inventaris.app', compact('barangs', 'ruangan', 'ajuan'));
    }

    public function unit(Request $request, $id)
    {
        $ruangan = Ruangan::all();

        $query = Barang::with('ruangan', 'barangMaster')
            ->where('barang_id', $id)->where('sedia', 1);

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('kondisi_barang')) {
            $query->where('kondisi_barang', $request->kondisi_barang);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tahun_perolehan', $request->tahun);
        }

        if ($request->filled('sumber_dana')) {
            $query->where('sumber_dana', $request->sumber_dana);
        }

        $barangs = $query->paginate(12);

        $barang = Barang::where('id', $id)->first();

        $tahunList = Barang::selectRaw('YEAR(tahun_perolehan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('inventaris.unit', compact('barangs', 'ruangan', 'tahunList', 'barang'));
    }

    public function show($id)
    {
        $item = Barang::with(['ruangan', 'perawatanItem'])->where('kode_barang', $id)->firstOrFail();
        if ($item->sedia == -1) {
            return redirect()->route('inventaris.index');
        }
        $ruangan = Ruangan::all();
        // $peminjaman = Peminjaman::where('status_peminjaman', 'Dipinjam')
        //     ->whereHas('ajuan', function ($query) {
        //         $query->where('status', 'disetujui');
        //     })
        //     ->sum('jumlah_barang');

        // $perawatan = Perawatan::where('status', 'belum')
        //     ->whereHas('ajuan', function ($query) {
        //         $query->where('status', 'disetujui');
        //     })
        //     ->sum('jumlah');

        $qr = $item->kode_barang;
        $qrCode = QrCode::size(200)->generate($qr);

        return view('inventaris.detail', compact(
            'item',
            'qrCode',
            'ruangan'
            // , 'perawatan', 'peminjaman'
        ));
    }

    public function barangRusak(Request $request, $id)
    {
        $barang = Barang::where('kode_barang', $id)->first();
        $request->validate([
            'kondisi_barang' => 'required|in:baik,rusak,berat',
            'keterangan' => 'required|string|max:255',
            'gambar_barang' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $gambarPath = null;

        if ($request->hasFile('gambar_barang')) {
            $file = $request->file('gambar_barang');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/ajuan');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $gambarPath = 'uploads/ajuan/' . $filename;
        }

        BarangRusak::create([
            'barang_id' => $barang->id,
            'user_id' => Auth::user()->id,
            'kondisi_barang' => $request->input('kondisi_barang'),
            'kode_barang' => $id, // pastikan kolom ini ada di tabel BarangRusak
            'keterangan' => $request->input('keterangan'),
            'gambar_barang' => $gambarPath, // pastikan kolom ini juga ada
        ]);

        return redirect()->back()->with('success', 'Barang rusak berhasil diajukan.');
    }

    public function aksi(Request $request)
    {
        // 1. Ambil selected IDs (berbentuk string "1,2,5,…"), ubah jadi array
        $idsCsv = $request->input('selected_ids', '');
        $ids = [];
        if (!empty($idsCsv)) {
            // explode dan filter agar tidak ada elemen kosong
            $ids = array_filter(explode(',', $idsCsv), function ($v) {
                return is_numeric($v);
            });
        }

        // 2. Pastikan ada setidaknya satu ID yang dipilih
        if (empty($ids)) {
            return redirect()->back()
                ->with('error', 'Tidak ada barang terpilih.');
        }

        // 3. Action type: "delete", "peminjaman", "perawatan", atau "mutasi"
        $action = $request->input('action_type');

        switch ($action) {
            case 'delete':
                return $this->handleDelete($request, $ids);

            case 'peminjaman':
                return $this->handlePeminjaman($request, $ids);

            case 'perawatan':
                return $this->handlePerawatan($request, $ids);

            case 'mutasi':
                return $this->handleMutasi($request, $ids);

            case 'cetak_qr_kecil':
                return $this->handleCetakQr($ids, 'inventaris.kecil');

            case 'cetak_qr_besar':
                return $this->handleCetakQr($ids, 'inventaris.besar');

            default:
                return redirect()->back()
                    ->with('error', 'Aksi tidak dikenal.');
        }
    }

    private function tidakTersedia(array $ids, $identifier)
    {
        Barang::whereIn('id', $ids)->update(['sedia' => $identifier]);
    }

    /**
     * Hapus semua barang yang ID‐nya ada di $ids
     */
    protected function handleDelete(Request $request, array $ids)
    {
        $request->validate([
            'keterangan_penghapusan' => 'required|string|max:255',
        ]);

        // Buat data pengajuan penghapusan
        $penghapusan = Penghapusan::create([
            'tanggal_pengajuan' => now(),
            'keterangan' => $request->input('keterangan_penghapusan'),
            'status' => 'diajukan', // misal: status bisa 'diajukan', 'disetujui', 'ditolak'
            'user_id' => Auth::id(), // jika ingin menyimpan siapa yang mengajukan
        ]);

        // Simpan item-item yang diajukan untuk dihapus
        foreach ($ids as $barang_id) {
            PenghapusanItem::create([
                'penghapusan_id' => $penghapusan->id,
                'barang_id' => $barang_id,
            ]);
        }
        $this->tidakTersedia($ids, 5);

        return redirect()->route('inventaris.index')
            ->with('success', 'Pengajuan penghapusan berhasil dibuat untuk barang terpilih.');
    }

    /**
     * Tangani pengajuan Peminjaman
     */
    protected function handlePeminjaman(Request $request, array $ids)
    {
        // Validasi form Peminjaman
        $request->validate([
            'tanggal_peminjaman'   => 'required|date',
            'nama_peminjam'        => 'required|string|max:255',
        ]);

        // Simpan ke tabel peminjaman
        $peminjaman = Peminjaman::create([
            'tanggal_peminjaman'   => $request->input('tanggal_peminjaman'),
            'tanggal_pengembalian' => null,
            'nama_peminjam'        => $request->input('nama_peminjam'),
            'keterangan'           => $request->input('keterangan'),
            'user_id'              => Auth::id(),
            'status_ajuan'         => 'pending',
        ]);

        // Buat entri PeminjamanItem untuk setiap barang terpilih
        foreach ($ids as $barangId) {
            PeminjamanItem::create([
                'barang_id'      => $barangId,
                'peminjaman_id'  => $peminjaman->id,
                'status_peminjaman' => 'Dipinjam',
            ]);
        }
        $this->tidakTersedia($ids, 2);

        return redirect()->route('inventaris.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    /**
     * Tangani pengajuan Perawatan
     */
    protected function handlePerawatan(Request $request, array $ids)
    {
        // Validasi form Perawatan
        $request->validate([
            'tanggal_perawatan' => 'required|date',
            'jenis_perawatan'   => 'required|string|max:255',
            'biaya_perawatan'   => 'nullable|integer|min:0',
            // 'keterangan' boleh kosong
        ]);

        // Simpan ke tabel perawatans
        $perawatan = Perawatan::create([
            'tanggal_perawatan' => $request->input('tanggal_perawatan'),
            'jenis_perawatan'   => $request->input('jenis_perawatan'),
            'biaya_perawatan'   => $request->input('biaya_perawatan'),
            'keterangan'        => $request->input('keterangan'),
            'user_id'           => Auth::id(),
            'status_ajuan'      => 'pending',
        ]);

        // Buat entri PerawatanItem untuk setiap barang terpilih
        foreach ($ids as $barangId) {
            PerawatanItem::create([
                'barang_id'       => $barangId,
                'perawatan_id'    => $perawatan->id,
                'status_perawatan' => 'belum',
            ]);
        }
        $this->tidakTersedia($ids, 3);

        return redirect()->route('inventaris.index')
            ->with('success', 'Pengajuan perawatan berhasil dikirim.');
    }

    /**
     * Tangani pengajuan Mutasi
     */
    protected function handleMutasi(Request $request, array $ids)
    {
        // Validasi form Mutasi
        $request->validate([
            'tanggal_mutasi' => 'required|date',
            'nama_mutasi'    => 'required|string|max:255',
            'tujuan'         => 'required|integer|exists:ruangans,id',
            // 'keterangan' boleh kosong
        ]);

        // Simpan ke tabel mutasis
        $mutasi = Mutasi::create([
            'tanggal_mutasi' => $request->input('tanggal_mutasi'),
            'nama_mutasi'    => $request->input('nama_mutasi'),
            'tujuan'         => $request->input('tujuan'),
            'keterangan'     => $request->input('keterangan'),
            'user_id'        => Auth::id(),
            'status_ajuan'   => 'pending',
        ]);

        // Buat entri MutasiItem untuk setiap barang terpilih
        foreach ($ids as $barangId) {
            MutasiItem::create([
                'barang_id'   => $barangId,
                'mutasi_id'   => $mutasi->id,
                'status_mutasi' => 'belum',
            ]);
        }
        $this->tidakTersedia($ids, 4);

        return redirect()->route('inventaris.index')
            ->with('success', 'Pengajuan mutasi berhasil dikirim.');
    }

    protected function handleCetakQr(array $ids, string $view)
    {
        // ambil barangs yang dipilih
        $barangs = Barang::whereIn('id', $ids)
            ->orderBy('kode_barang')
            ->get();

        return view($view, compact('barangs'));
    }

    public function scanResult(Request $request)
    {
        $fullUrl = $request->input('url'); // Kirimkan URL hasil scan sebagai parameter 'url'

        // Validasi: URL harus dari domain kamu
        $parsed = parse_url($fullUrl);
        if (!isset($parsed['host']) || $parsed['host'] !== request()->getHost()) {
            abort(403, 'QR Code tidak valid atau berasal dari domain lain.');
        }

        // Pisahkan path, ambil kode di segmen ke-3 (0-based: 0=/inventaris, 1=/unit, 2=KRS-00016)
        $segments = explode('/', trim($parsed['path'], '/'));
        $kode = $segments[2] ?? null;

        if (!$kode) {
            abort(400, 'QR Code tidak memiliki kode yang valid.');
        }
        
        $item = Barang::where('kode_barang', $kode)->firstOrFail();

        return view('inventaris.detail', compact('item'));
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'nama_barang'       => 'required|string|max:255',
    //             'jenis_barang'      => 'required|string',
    //             'merk_barang'       => 'required|string',
    //             'tahun_perolehan'   => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
    //             'sumber_dana'       => 'required|in:bos,dak,hibah',
    //             'harga_perolehan'   => 'nullable|numeric',
    //             'cv_pengadaan'      => 'nullable|string',
    //             'jumlah_barang'     => 'required|integer',
    //             'ruangan_id'        => 'required|exists:ruangans,id',
    //             'kondisi'           => 'nullable|string',
    //             'kepemilikan'       => 'required|string',
    //             'penanggung_jawab'  => 'nullable|string',
    //             'upload'            => 'nullable|image|mimes:jpeg,png,jpg,svg+xml,webp,gif,heic|max:2048',
    //         ]);
    //     } catch (ValidationException $e) {
    //         // Menyimpan ID modal yang harus dibuka kembali (contoh: 'TambahData')
    //         return redirect()->back()
    //             ->withErrors($e->validator)
    //             ->withInput()
    //             ->with('modal_error', 'TambahData');
    //     }

    //     // Tangani upload file
    //     if ($request->hasFile('upload')) {
    //         $file = $request->file('upload');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $path = public_path('uploads/inventaris');

    //         if (!file_exists($path)) {
    //             mkdir($path, 0777, true);
    //         }

    //         $file->move($path, $filename);
    //         $validated['gambar_barang'] = 'uploads/inventaris/' . $filename;
    //     }

    //     // Tambah kode unik dan normalisasi field
    //     $validated['kode_barang']         = 'BRG-' . strtoupper(Str::random(6));
    //     $validated['kondisi_barang']      = $validated['kondisi'] ?? null;
    //     $validated['kepemilikan_barang']  = $validated['kepemilikan'];

    //     unset($validated['kondisi'], $validated['kepemilikan']);

    //     // Simpan barang
    //     $barang = Barang::create($validated);

    //     // Simpan pengajuan
    //     // AjuanPengadaan::create([
    //     //     'user_id'   => Auth::id(),
    //     //     'barang_id' => $barang->id,
    //     // ]);

    //     return redirect('/inventaris')->with('success', 'Data inventaris berhasil ditambahkan.');
    // }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_barang' => 'required|string|max:255',
    //         'jenis_barang' => 'required|string|max:255',
    //         'merk_barang' => 'required|string|max:255', // wajib karena tidak nullable
    //         'tahun_perolehan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
    //         'sumber_dana' => 'required|in:BOS,DAK,Hibah', // ENUM
    //         'harga_perolehan' => 'nullable|numeric|min:0',
    //         'cv_pengadaan' => 'nullable|string|max:255',
    //         'jumlah_barang' => 'required|integer|min:1',
    //         'ruangan_id' => 'required|exists:ruangans,id', // foreign key
    //         'kondisi_barang' => 'required|in:baik,rusak,berat', // ENUM kondisi_barang
    //         'kepemilikan' => 'required|string|max:255',
    //         'penanggung_jawab' => 'nullable|string|max:255',
    //         'upload' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    //     ]);

    //     $barang = Barang::findOrFail($id);

    //     $barang->update($request->except('upload'));

    //     if ($request->hasFile('upload')) {
    //         $gambar = $request->file('upload')->store('uploads', 'public');
    //         $barang->gambar_barang = 'storage/' . $gambar;
    //         $barang->save();
    //     }

    //     return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     $barang = Barang::findOrFail($id);
    //     $barang->delete();
    //     return redirect('/inventaris')->with('success', 'Data inventaris berhasil dihapus.');
    // }

    // public function destroyApp(Request $request, $id)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'jumlah'     => 'required|integer|min:1',
    //             'keterangan' => 'nullable|string',
    //         ]);
    //     } catch (ValidationException $e) {
    //         return redirect()->back()
    //             ->withErrors($e->validator)
    //             ->withInput()
    //             ->with('modal_error', 'deleteModal' . $id); // otomatis target modal sesuai ID
    //     }

    //     $validated['barang_id'] = $id;

    //     $barang = Barang::findOrFail($id);

    //     if ($validated['jumlah'] > $barang->jumlah_barang) {
    //         return redirect()->back()
    //             ->withErrors(['jumlah' => 'Jumlah penghapusan tidak boleh melebihi jumlah barang yang ada.'])
    //             ->withInput()
    //             ->with('modal_error', 'deleteModal' . $id); // pastikan modal benar muncul
    //     }

    //     $penghapusan = Penghapusan::create($validated);

    //     AjuanPenghapusan::create([
    //         'user_id'         => Auth::id(),
    //         'penghapusan_id'  => $penghapusan->id,
    //     ]);

    //     return redirect('/inventaris')->with('success', 'Ajuan penghapusan berhasil diajukan.');
    // }

    public function barangMasuk()
    {
        $latestYear = Barang::max('tahun_perolehan');

        $dataInventaris = Barang::where('tahun_perolehan', $latestYear)
            ->paginate(10);

        return view('dashboard.barangMasuk', compact('dataInventaris', 'latestYear'));
    }
}
