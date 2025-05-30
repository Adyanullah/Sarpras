<?php

namespace App\Http\Controllers;

use App\Models\AjuanPenghapusan;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Penghapusan;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{

    public function index(Request $request)
    {
        // $query = Barang::with('ruangan')->where('jumlah_barang', '>', 0);
        // $barangs = Barang::with('ruangan')->get();

        // // Filter berdasarkan ruangan_id
        // if ($request->filled('ruangan_id')) {
        //     $query->where('ruangan_id', $request->ruangan_id);
        // }

        // // Filter berdasarkan keyword pencarian
        // if ($request->filled('search')) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('kode_barang', 'like', '%' . $request->search . '%')
        //             ->orWhere('nama_barang', 'like', '%' . $request->search . '%');
        //     });
        // }

        // // Ambil hasil dengan pagination
        // $dataInventaris = $query->paginate(8)->appends($request->query());

        // $ruangan = Ruangan::all();
        // return view('inventaris.app', compact('dataInventaris', 'ruangan', 'barangs'));
        $ruangan = Ruangan::all();
        $barangs = Barang::with('ruangan', 'barangDetail')
            ->groupBy('barang_id')
            ->paginate(8)
            ->appends($request->query());
        return view('inventaris.app', compact('barangs', 'ruangan'));
    }

    public function show($id)
    {
        $item = Barang::with(['ruangan', 'perawatan'])->findOrFail($id);
        $ruangans = Ruangan::all();
        $peminjaman = Peminjaman::where('status_peminjaman', 'Dipinjam')
            ->whereHas('ajuan', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->sum('jumlah_barang');

        $perawatan = Perawatan::where('status', 'belum')
            ->whereHas('ajuan', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->sum('jumlah');

        $qr = $item->kode_barang;
        $qrCode = QrCode::size(200)->generate($qr);

        return view('inventaris.detail', compact('item', 'qrCode', 'perawatan', 'peminjaman', 'ruangans'));
    }
    public function scanResult($kode)
    {
        $item = Barang::where('kode_barang', $kode)->firstOrFail();
        $qr = $item->kode_barang;

        $qrCode = QrCode::size(200)->generate($qr);
        return view('inventaris.detail', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_barang'       => 'required|string|max:255',
                'jenis_barang'      => 'required|string',
                'merk_barang'       => 'required|string',
                'tahun_perolehan'   => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
                'sumber_dana'       => 'required|in:bos,dak,hibah',
                'harga_perolehan'   => 'nullable|numeric',
                'cv_pengadaan'      => 'nullable|string',
                'jumlah_barang'     => 'required|integer',
                'ruangan_id'        => 'required|exists:ruangans,id',
                'kondisi'           => 'nullable|string',
                'kepemilikan'       => 'required|string',
                'penanggung_jawab'  => 'nullable|string',
                'upload'            => 'nullable|image|mimes:jpeg,png,jpg,svg+xml,webp,gif,heic|max:2048',
            ]);
        } catch (ValidationException $e) {
            // Menyimpan ID modal yang harus dibuka kembali (contoh: 'TambahData')
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'TambahData');
        }

        // Tangani upload file
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/inventaris');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $validated['gambar_barang'] = 'uploads/inventaris/' . $filename;
        }

        // Tambah kode unik dan normalisasi field
        $validated['kode_barang']         = 'BRG-' . strtoupper(Str::random(6));
        $validated['kondisi_barang']      = $validated['kondisi'] ?? null;
        $validated['kepemilikan_barang']  = $validated['kepemilikan'];

        unset($validated['kondisi'], $validated['kepemilikan']);

        // Simpan barang
        $barang = Barang::create($validated);

        // Simpan pengajuan
        // AjuanPengadaan::create([
        //     'user_id'   => Auth::id(),
        //     'barang_id' => $barang->id,
        // ]);

        return redirect('/inventaris')->with('success', 'Data inventaris berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'merk_barang' => 'required|string|max:255', // wajib karena tidak nullable
            'tahun_perolehan' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'sumber_dana' => 'required|in:BOS,DAK,Hibah', // ENUM
            'harga_perolehan' => 'nullable|numeric|min:0',
            'cv_pengadaan' => 'nullable|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'ruangan_id' => 'required|exists:ruangans,id', // foreign key
            'kondisi_barang' => 'required|in:baik,rusak,berat', // ENUM kondisi_barang
            'kepemilikan' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'upload' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $barang = Barang::findOrFail($id);

        $barang->update($request->except('upload'));

        if ($request->hasFile('upload')) {
            $gambar = $request->file('upload')->store('uploads', 'public');
            $barang->gambar_barang = 'storage/' . $gambar;
            $barang->save();
        }

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return redirect('/inventaris')->with('success', 'Data inventaris berhasil dihapus.');
    }

    public function destroyApp(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'jumlah'     => 'required|integer|min:1',
                'keterangan' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('modal_error', 'deleteModal' . $id); // otomatis target modal sesuai ID
        }

        $validated['barang_id'] = $id;

        $barang = Barang::findOrFail($id);

        if ($validated['jumlah'] > $barang->jumlah_barang) {
            return redirect()->back()
                ->withErrors(['jumlah' => 'Jumlah penghapusan tidak boleh melebihi jumlah barang yang ada.'])
                ->withInput()
                ->with('modal_error', 'deleteModal' . $id); // pastikan modal benar muncul
        }

        $penghapusan = Penghapusan::create($validated);

        AjuanPenghapusan::create([
            'user_id'         => Auth::id(),
            'penghapusan_id'  => $penghapusan->id,
        ]);

        return redirect('/inventaris')->with('success', 'Ajuan penghapusan berhasil diajukan.');
    }

    public function barangMasuk()
    {
        $latestYear = Barang::max('tahun_perolehan');

        $dataInventaris = Barang::where('tahun_perolehan', $latestYear)
            ->paginate(10);

        return view('dashboard.barangMasuk', compact('dataInventaris', 'latestYear'));
    }
}
