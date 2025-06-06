<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengadaan;
use App\Models\BarangMaster;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Perawatan;
use App\Models\PerawatanItem;
use App\Models\Mutasi;
use App\Models\MutasiItem;
use App\Models\Penghapusan;
use App\Models\PenghapusanItem;
use Illuminate\Support\Str;

class AjuanController extends Controller
{
    public function index()
    {
        $dataAjuan = collect();

        // 1. Pengadaan (status = pending)
        $pengadaans = Pengadaan::with(['user', 'barangMaster', 'ruangan'])->where('status', 'pending')->get();
        foreach ($pengadaans as $p) {
            $namaBarang = $p->tipe_pengajuan === 'tambah'
                ? optional($p->barangMaster)->nama_barang
                : $p->nama_barang;
            $ruanganAsal = optional($p->ruangan)->nama_ruangan ?? '-';
            // (tidak ada "tujuan" untuk pengadaan)
            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Pengadaan',
                'barang'     => $p->nama_barang,
                'jumlah'     => $p->jumlah,
                'status'     => $p->status,
                'ruangan'    => $ruanganAsal,
                'tambahan'   => null,               
                'model_type' => 'pengadaan',
                'keterangan' => $p->catatan ?? '-',
            ]);
        }

        // 2. Peminjaman (status_ajuan = pending)
        $peminjamans = Peminjaman::with(['user', 'peminjamanItem.barang'])->where('status_ajuan', 'pending')->get();
        foreach ($peminjamans as $p) {
            
            $namaBarang = $p->peminjamanItem->first()->barang->barangMaster->nama_barang;
            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Peminjaman',
                'barang'     => $namaBarang,
                'jumlah'     => $p->peminjamanItem->count(),
                'status'     => $p->status_ajuan,
                'ruangan'    => '-',
                'tambahan'   => null,
                'model_type' => 'peminjaman',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        // 3. Perawatan (status_ajuan = pending)
        $perawatans = Perawatan::with(['user'])->where('status_ajuan', 'pending')->get();
        foreach ($perawatans as $p) {
            $namaBarang = $p->pewaratanItem->first()->barang->barangMaster->nama_barang;
            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Perawatan',
                'barang'     => $namaBarang,
                'jumlah'     => $p->pewaratanItem->count(),
                'status'     => $p->status_ajuan,
                'ruangan'    => '-',
                'tambahan'   => null,
                'model_type' => 'perawatan',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        // 4. Mutasi (status_ajuan = pending)
        $mutasis = Mutasi::with(['user'])->where('status_ajuan', 'pending')->get();
        foreach ($mutasis as $m) {
            $namaBarang = $m->items->first()->barang->barangMaster->nama_barang;
            $ruanganAsal = $m->items->first() 
                ? optional($m->items->first()->barang->ruangan)->nama_ruangan 
                : '-';
            $ruanganTujuan = $m->tujuan; // diasumsikan angka ID ruangan; bisa cari nama jika relasi ada

            $dataAjuan->push([
                'id'         => $m->id,
                'created_at' => $m->created_at->format('d M Y'),
                'pengaju'    => $m->user->name,
                'jenis'      => 'Mutasi',
                'barang'     => $namaBarang,
                'jumlah'     => $m->items->count(),
                'status'     => $m->status_ajuan,
                'ruangan'    => $ruanganAsal,
                'tambahan'   => $ruanganTujuan,
                'model_type' => 'mutasi',
                'keterangan' => $m->keterangan ?? '-',
            ]);
        }

        // 5. Penghapusan (status = pending)
        $penghapusans = Penghapusan::with(['user'])->where('status_ajuan', 'pending')->get();
        foreach ($penghapusans as $p) {
            $namaBarang = $p->items->first()->barang->barangMaster->nama_barang;

            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Penghapusan',
                'barang'     => $namaBarang,
                'jumlah'     => $p->items->count(),
                'status'     => $p->status_ajuan,
                'ruangan'    => '-',                  // tidak relevan di penghapusan
                'tambahan'   => null,
                'model_type' => 'penghapusan',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        // Oper variabel ke view
        return view('ajuan.app', [
            'dataAjuan' => $dataAjuan
        ]);
    }

    /**
     * Update status ajuan (Disetujui / Ditolak).
     * Route: PUT /waka/verifikasi-ajuan/{type}/{id}/{status}
     */
    public function updateStatus(
        Request $request,
        string $type,
        int $id,
        string $status
    ) {
        // Validasi tipe dan status
        if (! in_array($type, ['pengadaan', 'peminjaman', 'perawatan', 'mutasi', 'penghapusan'])) {
            return redirect()->back()->with('error', 'Tipe ajuan tidak valid.');
        }
        if (! in_array($status, ['Disetujui', 'Ditolak'])) {
            return redirect()->back()->with('error', 'Status harus Disetujui atau Ditolak.');
        }

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'pengadaan':
                    $ajuan = Pengadaan::findOrFail($id);
                    if ($status === 'Disetujui') {
                        $this->approvePengadaan($ajuan);
                        $ajuan->status = 'disetujui';
                    } else {
                        $ajuan->status = 'ditolak';
                    }
                    $ajuan->save();
                    break;

                case 'peminjaman':
                    $ajuan = Peminjaman::with('items')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        foreach ($ajuan->items as $item) {
                            $item->status_peminjaman = 'Dipinjam';
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 0; // 0 = tidak tersedia
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                    }
                    $ajuan->save();
                    break;

                case 'perawatan':
                    $ajuan = Perawatan::with('items')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        foreach ($ajuan->items as $item) {
                            $item->status_perawatan = 'selesai';
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 0;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                    }
                    $ajuan->save();
                    break;

                case 'mutasi':
                    $ajuan = Mutasi::with('items')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        $tujuan = $ajuan->tujuan;
                        foreach ($ajuan->items as $item) {
                            $item->status_mutasi = 'selesai';
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->ruangan_id = $tujuan;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                    }
                    $ajuan->save();
                    break;

                case 'penghapusan':
                    $ajuan = Penghapusan::with('items')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status = 'disetujui';
                        foreach ($ajuan->items as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 0;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status = 'ditolak';
                    }
                    $ajuan->save();
                    break;
            }

            DB::commit();
            return redirect()->back()->with('success', 'Ajuan telah di' . strtolower($status) . '.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses ajuan: ' . $e->getMessage());
        }
    }

    /**
     * Logika pembuatan barang baru untuk Pengadaan.
     */
    protected function approvePengadaan(Pengadaan $p)
    {
        // Jika tipe 'tambah', pakai barangMaster yang ada
        if ($p->tipe_pengajuan === 'tambah' && $p->barang_master_id) {
            $master = BarangMaster::findOrFail($p->barang_master_id);
            for ($i = 0; $i < $p->jumlah; $i++) {
                Barang::create([
                    'barang_id'         => $master->id,
                    // asumsi generate kode unik (Anda bisa sesuaikan logika)
                    'kode_barang'       => $master->kode_barang . '_' . strtoupper(\Str::random(4)),
                    'tahun_perolehan'   => $p->tahun_perolehan,
                    'sumber_dana'       => $p->sumber_dana,
                    'harga_unit'        => $p->harga_perolehan,
                    'cv_pengadaan'      => $p->cv_pengadaan,
                    'ruangan_id'        => $p->ruangan_id,
                    'kondisi_barang'    => $p->kondisi_barang ?? 'baik',
                    'kepemilikan_barang'=> $p->kepemilikan_barang ?? $master->kepemilikan_barang,
                    'sedia'             => 1,
                ]);
            }
        }
        // Jika tipe 'baru', buat master baru lalu tambah barang
        elseif ($p->tipe_pengajuan === 'baru') {
            $newMaster = BarangMaster::create([
                'kode_barang' => $p->kode_barang,
                'nama_barang' => $p->nama_barang,
                'jenis_barang'=> $p->jenis_barang,
                'merk_barang' => $p->merk_barang,
            ]);
            for ($i = 0; $i < $p->jumlah; $i++) {
                Barang::create([
                    'barang_id'         => $newMaster->id,
                    'kode_barang'       => $newMaster->kode_barang . '_' . strtoupper(\Str::random(4)),
                    'tahun_perolehan'   => $p->tahun_perolehan,
                    'sumber_dana'       => $p->sumber_dana,
                    'harga_unit'        => $p->harga_perolehan,
                    'cv_pengadaan'      => $p->cv_pengadaan,
                    'ruangan_id'        => $p->ruangan_id,
                    'kondisi_barang'    => $p->kondisi_barang ?? 'baik',
                    'kepemilikan_barang'=> $p->kepemilikan_barang,
                    'sedia'             => 1,
                ]);
            }
        }
        // selain itu, tidak ada aksi
    }
}
