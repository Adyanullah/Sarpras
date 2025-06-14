<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengadaan;
use App\Models\BarangMaster;
use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Perawatan;
use App\Models\PerawatanItem;
use App\Models\Mutasi;
use App\Models\MutasiItem;
use App\Models\Penghapusan;
use App\Models\PenghapusanItem;
use App\Models\Ruangan;
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
            $jenis = 'Pengadaan ' . ($p->tipe_pengajuan === 'baru' ? 'Baru' : 'Tambah');
            $ruanganAsal = optional($p->ruangan)->nama_ruangan ?? '-';
            // (tidak ada "tujuan" untuk pengadaan)
            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => $jenis,
                'barang'     => $namaBarang,
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
        $perawatans = Perawatan::with(['user', 'perawatanItem.barang'])->where('status_ajuan', 'pending')->get();
        foreach ($perawatans as $p) {
            $namaBarang = $p->perawatanItem->first()->barang->barangMaster->nama_barang;
            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Perawatan',
                'barang'     => $namaBarang,
                'jumlah'     => $p->perawatanItem->count(),
                'status'     => $p->status_ajuan,
                'ruangan'    => '-',
                'tambahan'   => null,
                'model_type' => 'perawatan',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        // 4. Mutasi (status_ajuan = pending)
        $mutasis = Mutasi::with(['user'])->where('status_ajuan', 'pending')->get();
        // Ambil semua ID tujuan
        $tujuanIds = $mutasis->pluck('tujuan')->unique()->filter();

        // Ambil semua data ruangan sekaligus
        $ruangans = Ruangan::whereIn('id', $tujuanIds)->pluck('nama_ruangan', 'id');
        foreach ($mutasis as $m) {
            $namaBarang = $m->mutasiItem->first()->barang->barangMaster->nama_barang;
            $ruanganAsal = $m->mutasiItem->first()
                ? optional($m->mutasiItem->first()->barang->ruangan)->nama_ruangan
                : '-';
            $ruanganTujuan = $ruangans[$m->tujuan] ?? '-'; // diasumsikan angka ID ruangan; bisa cari nama jika relasi ada

            $dataAjuan->push([
                'id'         => $m->id,
                'created_at' => $m->created_at->format('d M Y'),
                'pengaju'    => $m->user->name,
                'jenis'      => 'Mutasi',
                'barang'     => $namaBarang,
                'jumlah'     => $m->mutasiItem->count(),
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
            $namaBarang = $p->penghapusanItem()->first()->barang->barangMaster->nama_barang;

            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => 'Penghapusan',
                'barang'     => $namaBarang,
                'jumlah'     => $p->penghapusanItem()->count(),
                'status'     => $p->status_ajuan,
                'ruangan'    => '-',                  
                'tambahan'   => null,
                'model_type' => 'penghapusan',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        // 6. Barang Rusak (status = pending)
        $barangRusak = BarangRusak::with(['user','barang'])->where('status_ajuan', 'pending')->get();
        foreach ($barangRusak as $r) {
            $namaBarang = $r->barang->barangMaster->nama_barang;
            $ruangan = optional(optional($r->barang)->ruangan)->nama_ruangan ?? 'Ruangan tidak ditemukan';
            if ($r->kondisi_barang == 'rusak') {
                $kondisi = 'Rusak Ringan';
            } elseif ($r->kondisi_barang == 'berat') {
                $kondisi = 'Rusak Berat';
            }
            $dataAjuan->push([
                'id'         => $r->id,
                'created_at' => $r->created_at->format('d M Y'),
                'pengaju'    => $r->user->name,
                'jenis'      => 'Barang Rusak - '. $kondisi,
                'barang'     => $namaBarang,
                'jumlah'     => $r->barang->kode_barang,
                'status'     => $r->status_ajuan,
                'ruangan'    => $ruangan,                  
                'tambahan'   => $r->gambar_barang ?? null,
                'model_type' => 'barang_rusak',
                'keterangan' => $r->keterangan ?? '-',
            ]);
        }

        // Oper variabel ke view
        return view('ajuan.app', [
            'dataAjuan' => $dataAjuan
        ]);
    }

    private function tersedia($barangs)
    {
        foreach ($barangs as $barang) {
            if ($barang) {
                $barang->sedia = 1;
                $barang->save();
            }
        }
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
        if (! in_array($type, ['pengadaan', 'peminjaman', 'perawatan', 'mutasi', 'penghapusan','barang_rusak'])) {
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
                    $ajuan = Peminjaman::with('peminjamanItem.barang')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        $ajuan->status_peminjaman = 'Dipinjam';
                        foreach ($ajuan->peminjamanItem as $item) {
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 0; // 0 = tidak tersedia
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                        foreach ($ajuan->peminjamanItem as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 1;
                                $barang->save();
                            }
                        }
                        $this->tersedia($ajuan->peminjamanItem->pluck('barang'));
                    }
                    $ajuan->save();
                    break;

                case 'perawatan':
                    $ajuan = Perawatan::with('perawatanItem')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        foreach ($ajuan->perawatanItem as $item) {
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 0;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                        foreach ($ajuan->perawatanItem as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 1;
                                $barang->save();
                            }
                        }
                        $this->tersedia($ajuan->perawatanItem->pluck('barang'));
                    }
                    $ajuan->save();
                    break;

                case 'mutasi':
                    $ajuan = Mutasi::with('mutasiItem')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        $tujuan = $ajuan->tujuan;
                        $ajuan->status_mutasi = 'selesai';
                        foreach ($ajuan->mutasiItem as $item) {
                            $item->save();
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->ruangan_id = $tujuan;
                                $barang->sedia = 1;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                        foreach ($ajuan->mutasiItem as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 1;
                                $barang->save();
                            }
                        }
                        $this->tersedia($ajuan->mutasiItem->pluck('barang'));
                    }
                    $ajuan->save();
                    break;

                case 'penghapusan':
                    $ajuan = Penghapusan::with('penghapusanItem')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        foreach ($ajuan->penghapusanItem as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = -1;
                                $barang->save();
                            }
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
                        foreach ($ajuan->penghapusanItem as $item) {
                            $barang = $item->barang;
                            if ($barang) {
                                $barang->sedia = 1;
                                $barang->save();
                            }
                        }
                        $this->tersedia($ajuan->penghapusanItem->pluck('barang'));
                    }
                    $ajuan->save();
                    break;

                case 'barang_rusak':
                    $ajuan = BarangRusak::with('barang')->findOrFail($id);
                    if ($status === 'Disetujui') {
                        $ajuan->status_ajuan = 'disetujui';
                        $barang = $ajuan->barang;
                        $kondisi = $ajuan->kondisi_barang;
                        if ($barang) {
                            $barang->kondisi_barang = $kondisi;
                            $barang->save();
                        }
                    } else {
                        $ajuan->status_ajuan = 'ditolak';
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
        if ($p->tipe_pengajuan === 'tambah' && $p->barang_master_id) {
            $master = BarangMaster::with('barang')->findOrFail($p->barang_master_id);
            $this->createBarangFromPengadaan($master, $p);
        } elseif ($p->tipe_pengajuan === 'baru') {
            $newMaster = BarangMaster::create([
                'kode_barang'   => $p->kode_barang,
                'nama_barang'   => $p->nama_barang,
                'jenis_barang'  => $p->jenis_barang,
                'merk_barang'   => $p->merk_barang,
                'gambar_barang' => $p->gambar_barang ?? null,
            ]);
            $this->createBarangFromPengadaan($newMaster, $p);
        }
    }

    protected function createBarangFromPengadaan(BarangMaster $master, Pengadaan $p)
    {
        $prefix = $master->kode_barang;

        // Ambil kode terakhir dari barang dengan prefix tersebut
        $lastBarang = Barang::where('kode_barang', 'like', $prefix . '-%')
            ->orderByDesc('kode_barang')
            ->first();

        $lastNumber = 0;
        if ($lastBarang) {
            $lastNumber = (int) str_replace($prefix . '-', '', $lastBarang->kode_barang);
        }
        $nextNumber = $lastNumber + 1;

        for ($i = 0; $i < $p->jumlah; $i++) {
            $kodeBarangBaru = $this->generateKodeBarang($prefix, $nextNumber++);
            Barang::create([
                'barang_id'          => $master->id,
                'kode_barang'        => $kodeBarangBaru,
                'tahun_perolehan'    => $p->tahun_perolehan ?? date('Y'),
                'sumber_dana'        => $p->sumber_dana,
                'harga_unit'         => $p->harga_perolehan,
                'cv_pengadaan'       => $p->cv_pengadaan,
                'ruangan_id'         => $p->ruangan_id,
                'kondisi_barang'     => $p->kondisi_barang ?? 'baik',
                'keterangan'         => $p->keterangan ?? $master->keterangan,
                'sedia'              => 1,
            ]);
        }
    }

    protected function generateKodeBarang(string $prefix, int $number): string
    {
        return $prefix . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
