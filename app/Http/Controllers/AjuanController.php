<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Pengadaan;
use App\Models\BarangMaster;
use App\Models\Barang;
use App\Models\BarangRusak;
use App\Models\Peminjaman;
use App\Models\Perawatan;
use App\Models\Mutasi;
use App\Models\Penghapusan;
use App\Models\Ruangan;

class AjuanController extends Controller
{
    public function index()
    {
        $dataAjuan = collect();

        //
        // 1. PENGADAAN (master = pengadaans, detail = pengadaan_items)
        //
        $pengadaans = Pengadaan::with(['user', 'barangMaster', 'items.ruangan'])
            ->where('status', 'pending')
            ->get();

        foreach ($pengadaans as $p) {
            // Nama barang: jika tambah pakai master, jika baru pakai kolom sendiri
            $namaBarang = $p->tipe_pengajuan === 'tambah'
                ? optional($p->barangMaster)->nama_barang
                : $p->nama_barang;

            // Tipe ajuan
            $jenis = 'Pengadaan ' . ($p->tipe_pengajuan === 'baru' ? 'Baru' : 'Tambah');

            // Daftar ruangan (asal) unik, digabungkan
            $ruanganList = $p->items
                ->pluck('ruangan.nama_ruangan')
                ->filter()
                ->unique()
                ->toArray();
            $ruanganAsal = $ruanganList
                ? implode(', ', $ruanganList)
                : '-';

            // Total jumlah (sum di semua item)
            $jumlahTotal = $p->items->sum('jumlah');

            $dataAjuan->push([
                'id'         => $p->id,
                'created_at' => $p->created_at->format('d M Y'),
                'pengaju'    => $p->user->name,
                'jenis'      => $jenis,
                'barang'     => $namaBarang,
                'jumlah'     => $jumlahTotal,
                'status'     => $p->status,
                'ruangan'    => $ruanganAsal,
                'tambahan'   => null,
                'model_type' => 'pengadaan',
                'keterangan' => $p->keterangan ?? '-',
            ]);
        }

        //
        // 2. PEMINJAMAN
        //
        $peminjamans = Peminjaman::with(['user', 'peminjamanItem.barang.barangMaster'])
            ->where('status_ajuan', 'pending')
            ->get();
        foreach ($peminjamans as $p) {
            $namaBarang = $p->peminjamanItem
                ->first()
                ->barang
                ->barangMaster
                ->nama_barang;

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

        //
        // 3. PERAWATAN
        //
        $perawatans = Perawatan::with(['user', 'perawatanItem.barang.barangMaster'])
            ->where('status_ajuan', 'pending')
            ->get();
        foreach ($perawatans as $p) {
            $namaBarang = $p->perawatanItem
                ->first()
                ->barang
                ->barangMaster
                ->nama_barang;

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

        //
        // 4. MUTASI
        //
        $mutasis = Mutasi::with(['user', 'mutasiItem.barang.barangMaster', 'mutasiItem.barang.ruangan'])
            ->where('status_ajuan', 'pending')
            ->get();

        // preload nama ruangan tujuan
        $tujuanIds = $mutasis->pluck('tujuan')->unique()->filter();
        $ruangans  = Ruangan::whereIn('id', $tujuanIds)
            ->pluck('nama_ruangan', 'id');

        foreach ($mutasis as $m) {
            $firstItem = $m->mutasiItem->first();
            $namaBarang = optional($firstItem->barang->barangMaster)->nama_barang;
            $ruanganAsal = optional($firstItem->barang->ruangan)->nama_ruangan ?? '-';
            $ruanganTujuan = $ruangans[$m->tujuan] ?? '-';

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

        //
        // 5. PENGHAPUSAN
        //
        $penghapusans = Penghapusan::with(['user', 'penghapusanItem.barang.barangMaster'])
            ->where('status_ajuan', 'pending')
            ->get();
        foreach ($penghapusans as $p) {
            $firstItem  = $p->penghapusanItem()->first();
            $namaBarang = optional($firstItem->barang->barangMaster)->nama_barang;

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

        //
        // 6. BARANG RUSAK
        //
        $barangRusak = BarangRusak::with(['user', 'barang.barangMaster', 'barang.ruangan'])
            ->where('status_ajuan', 'pending')
            ->get();
        foreach ($barangRusak as $r) {
            $namaBarang = optional($r->barang->barangMaster)->nama_barang;
            $ruangan    = optional($r->barang->ruangan)->nama_ruangan ?? '-';
            $kondisi    = $r->kondisi_barang === 'berat' ? 'Rusak Berat' : 'Rusak Ringan';

            $dataAjuan->push([
                'id'         => $r->id,
                'created_at' => $r->created_at->format('d M Y'),
                'pengaju'    => $r->user->name,
                'jenis'      => 'Barang Rusak - ' . $kondisi,
                'barang'     => $namaBarang,
                'jumlah'     => $r->barang->kode_barang,
                'status'     => $r->status_ajuan,
                'ruangan'    => $ruangan,
                'tambahan'   => $r->gambar_barang ?? null,
                'model_type' => 'barang_rusak',
                'keterangan' => $r->keterangan ?? '-',
            ]);
        }

        return view('ajuan.app', [
            'dataAjuan' => $dataAjuan,
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
    public function updateStatus(Request $request, string $type, int $id, string $status)
    {
        // 1) Validasi
        $validTypes   = ['pengadaan','peminjaman','perawatan','mutasi','penghapusan','barang_rusak'];
        $validStatus  = ['Disetujui','Ditolak'];
        if (!in_array($type, $validTypes) || !in_array($status, $validStatus)) {
            return back()->with('error','Tipe atau status ajuan tidak valid.');
        }

        DB::beginTransaction();
        try {
            // 2) Load model & items
            $ajuan = $this->findAjuanModel($type, $id);

            // 3) Resolve handler
            $handler = resolve("ajuan.handler.{$type}");

            // 4) Jalankan approve atau reject
            if ($status === 'Disetujui') {
                $handler->approve($ajuan);
            } else {
                $handler->reject($ajuan);
            }

            DB::commit();
            return back()->with('success', "Ajuan {$type} telah di" . strtolower($status) . '.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal memproses ajuan: '.$e->getMessage());
        }
    }

    protected function findAjuanModel(string $type, int $id)
    {
        $map = [
            'pengadaan'    => \App\Models\Pengadaan::class,
            'peminjaman'   => \App\Models\Peminjaman::class,
            'perawatan'    => \App\Models\Perawatan::class,
            'mutasi'       => \App\Models\Mutasi::class,
            'penghapusan'  => \App\Models\Penghapusan::class,
            'barang_rusak' => \App\Models\BarangRusak::class,
        ];

        $modelClass = $map[$type];
        // Bila pakai relation `items` (untuk pengadaan, mutasi, penghapusan), atau `peminjamanItem`, dst.
        switch ($type) {
            case 'pengadaan':
                return $modelClass::with('items')->findOrFail($id);
            case 'peminjaman':
                return $modelClass::with('peminjamanItem.barang')->findOrFail($id);
            case 'perawatan':
                return $modelClass::with('perawatanItem.barang')->findOrFail($id);
            case 'mutasi':
                return $modelClass::with('mutasiItem.barang')->findOrFail($id);
            case 'penghapusan':
                return $modelClass::with('penghapusanItem.barang')->findOrFail($id);
            case 'barang_rusak':
                return $modelClass::with('barang')->findOrFail($id);
        }
    }

    /**
     * Logika pembuatan barang baru untuk Pengadaan.
     */
    // protected function approvePengadaan(Pengadaan $p)
    // {
    //     // 1) Jika barang baru, buat master-nya dulu
    //     if ($p->tipe_pengajuan === 'baru') {
    //         $master = BarangMaster::create([
    //             'kode_barang'   => $p->kode_barang,
    //             'nama_barang'   => $p->nama_barang,
    //             'jenis_barang'  => $p->jenis_barang,
    //             'merk_barang'   => $p->merk_barang,
    //             'gambar_barang' => $p->gambar_barang,
    //         ]);
    //     } else {
    //         // barang lama: ambil relasi
    //         $master = $p->barangMaster;
    //     }

    //     // 2) Buat stok per detail item
    //     $this->createBarangFromPengadaan($master, $p);

    //     // 3) Tandai request sudah disetujui
    //     $p->update(['status_ajuan' => 'disetujui']);
    // }

    // protected function createBarangFromPengadaan(BarangMaster $master, Pengadaan $p)
    // {
    //     $prefix = $master->kode_barang;

    //     // Cari kode terakhir di tabel barangs
    //     $last = Barang::where('kode_barang', 'like', $prefix . '-%')
    //         ->orderByDesc('kode_barang')
    //         ->first();

    //     $lastNumber = $last
    //         ? (int) Str::after($last->kode_barang, $prefix . '-')
    //         : 0;

    //     $nextNumber = $lastNumber + 1;

    //     $rows = [];

    //     // Loop setiap lokasi/jumlah di pengadaan_items
    //     foreach ($p->items as $item) {
    //         for ($i = 0; $i < $item->jumlah; $i++) {
    //             $kode = $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    //             $rows[] = [
    //                 'barang_id'        => $master->id,
    //                 'kode_barang'      => $kode,
    //                 'tahun_perolehan'  => $p->tahun_perolehan ?? now()->year,
    //                 'sumber_dana'      => $p->sumber_dana,
    //                 'harga_unit'       => $p->harga_perolehan,
    //                 'cv_pengadaan'     => $p->cv_pengadaan,
    //                 'ruangan_id'       => $item->ruangan_id,
    //                 'kondisi_barang'   => 'baik',
    //                 'keterangan'       => $p->keterangan,
    //                 'sedia'            => 1,
    //                 'created_at'       => now(),
    //                 'updated_at'       => now(),
    //             ];

    //             $nextNumber++;
    //         }
    //     }

    //     // Bulk insert semua barangs baru
    //     if (!empty($rows)) {
    //         Barang::insert($rows);
    //     }
    // }
}
