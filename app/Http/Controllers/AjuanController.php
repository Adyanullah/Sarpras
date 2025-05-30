<?php

namespace App\Http\Controllers;

use App\Models\AjuanMutasi;
use App\Models\AjuanPeminjaman;
use App\Models\AjuanPenghapusan;
use App\Models\AjuanPerawatan;
use App\Models\Barang;
use App\Models\Mutasi;
use App\Models\Pengadaan;
use App\Models\Ruangan;
use Illuminate\Support\Str;

class AjuanController extends Controller
{
    public function index()
    {
        $peminjaman = AjuanPeminjaman::with(['user', 'peminjaman.barang.ruangan'])->whereIn('status', ['pending', 'ditolak'])->get();
        $pengadaan = Pengadaan::with(['user', 'barang.ruangan'])->whereIn('status', ['pending', 'ditolak'])->where('tipe_pengajuan', 'tambah')->get();
        $pengadaanBaru = Pengadaan::with(['user', 'barang.ruangan'])->whereIn('status', ['pending', 'ditolak'])->where('tipe_pengajuan', 'baru')->get();
        $perawatan = AjuanPerawatan::with((['user', 'perawatan.barang.ruangan']))->whereIn('status', ['pending', 'ditolak'])->get();
        $mutasi = AjuanMutasi::with((['user', 'mutasi.barang.ruangan']))->whereIn('status', ['pending', 'ditolak'])->get();
        $penghapusan = AjuanPenghapusan::with((['user', 'penghapusan.barang.ruangan']))->whereIn('status', ['pending', 'ditolak'])->get();

        $dataAjuan = collect();

        foreach ($peminjaman as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'peminjaman',
                'created_at' => $item->created_at->format('Y-m-d'),
                'jenis' => 'Peminjaman',
                'pengaju' => $item->user->name ?? '-',
                'barang' => $item->peminjaman->barang->nama_barang ?? '-',
                'jumlah' => $item->peminjaman->jumlah_barang ?? '-',
                'status' => $item->status,
                'ruangan' => $item->peminjaman->barang->ruangan->nama_ruangan ?? '-',
                'keterangan' => $item->peminjaman->keterangan ?? '-',
                'tambahan' => '',
            ]);
        }

        foreach ($pengadaan as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'pengadaan',
                'created_at' => $item->created_at->format('Y-m-d'),
                'pengaju' => $item->user->name ?? '-',
                'jenis' => 'Pengadaan',
                'barang' => $item->barang->nama_barang ?? '-',
                'jumlah' => $item->jumlah ?? '-',
                'status' => $item->status,
                'ruangan' => $item->barang->ruangan->nama_ruangan ?? '-',
                'keterangan' => 'Tambah jumlah barang',
                'tambahan' => '',
            ]);
        }

        foreach ($pengadaanBaru as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'pengadaan',
                'created_at' => $item->created_at->format('Y-m-d'),
                'pengaju' => $item->user->name ?? '-',
                'jenis' => 'Pengadaan',
                'barang' => $item->nama_barang ?? '-',
                'jumlah' => $item->jumlah ?? '-',
                'status' => $item->status,
                'ruangan' => $item->ruangan->nama_ruangan ?? '-',
                'keterangan' => 'Barang baru',
                'tambahan' => $item->gambar_barang ?? '-',
            ]);
        }

        foreach ($perawatan as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'perawatan',
                'created_at' => $item->created_at->format('Y-m-d'),
                'pengaju' => $item->user->name ?? '-',
                'jenis' => 'Perawatan',
                'barang' => $item->perawatan->barang->nama_barang ?? '-',
                'jumlah' => $item->perawatan->jumlah ?? '-',
                'status' => $item->status,
                'ruangan' => $item->perawatan->barang->ruangan->nama_ruangan ?? '-',
                'keterangan' => $item->perawatan->keterangan ?? '-',
                'tambahan' => '',
            ]);
        }

        foreach ($penghapusan as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'penghapusan',
                'created_at' => $item->created_at->format('Y-m-d'),
                'pengaju' => $item->user->name ?? '-',
                'jenis' => 'Penghapusan',
                'barang' => $item->penghapusan->barang->nama_barang ?? '-',
                'jumlah' => $item->penghapusan->jumlah ?? '-',
                'status' => $item->status,
                'ruangan' => $item->penghapusan->barang->ruangan->nama_ruangan ?? '-',
                'keterangan' => $item->penghapusan->keterangan ?? '-',
                'tambahan' => '',
            ]);
        }

        foreach ($mutasi as $item) {
            $dataAjuan->push([
                'id' => $item->id,
                'model_type' => 'mutasi',
                'created_at' => $item->created_at->format('Y-m-d'),
                'pengaju' => $item->user->name ?? '-',
                'jenis' => 'Mutasi',
                'barang' => $item->mutasi->barang->nama_barang ?? '-',
                'jumlah' => $item->mutasi->jumlah_barang ?? '-',
                'status' => $item->status,
                'ruangan' => $item->mutasi->barang->ruangan->nama_ruangan ?? '-',
                'keterangan' => $item->mutasi->keterangan ?? '-',
                'tambahan' => Ruangan::find($item->mutasi->tujuan)->nama_ruangan ?? '-',
            ]);
        }
        $dataAjuan = $dataAjuan
            // ->where('status', 'pending')
            ->sortByDesc('created_at')->values();


        return view('ajuan.app', compact('dataAjuan'));
    }

    public function UpdateStatus($type, $id, $status)
    {
        if ($type === 'peminjaman') {
            $ajuan = AjuanPeminjaman::find($id);
        if (!$ajuan) {
            return redirect()->back()->with('error', 'Data ajuan tidak ditemukan.');
        }
            if ($status === 'Disetujui') {
                $peminjaman = $ajuan->peminjaman;
                $barang = Barang::findOrFail($peminjaman->barang_id);
                if ($peminjaman->jumlah_barang > $barang->jumlah_barang) {
                    return back()->withErrors(['jumlah_barang' => 'Jumlah barang yang diminta melebihi stok tersedia.'])->withInput();
                }
                $barang->jumlah_barang -= $peminjaman->jumlah_barang;
                $barang->save();
            }
        } elseif ($type === 'pengadaan') {
            $ajuan = Pengadaan::find($id);
            if (!$ajuan) {
                return redirect()->back()->with('error', 'Data pengadaan tidak ditemukan.');
            }

            if ($status === 'Disetujui') {
                if ($ajuan->tipe_pengajuan === 'tambah') {
                    $barang = Barang::find($ajuan->barang_id);
                    if (!$barang) {
                        return redirect()->back()->with('error', 'Barang yang ingin ditambah tidak ditemukan.');
                    }

                    // Cek apakah data detail barang masih sama
                    if (
                        $barang->nama_barang === $ajuan->nama_barang &&
                        $barang->merk_barang === $ajuan->merk_barang &&
                        $barang->jenis_barang === $ajuan->jenis_barang &&
                        $barang->ruangan_id === $ajuan->ruangan_id &&
                        $barang->kondisi_barang === ($ajuan->kondisi_barang ?? 'baik')
                    ) {
                        // Jika sama, cukup tambahkan jumlah
                        $barang->jumlah_barang += $ajuan->jumlah;
                        $barang->save();
                    } else {
                        // Jika beda, buat barang baru
                        Barang::create([
                            'kode_barang' => 'BRG-' . strtoupper(Str::random(6)),
                            'kode_asal' => $barang->kode_barang, // untuk tracking asal
                            'nama_barang' => $ajuan->nama_barang,
                            'jenis_barang' => $ajuan->jenis_barang,
                            'merk_barang' => $ajuan->merk_barang,
                            'tahun_perolehan' => $ajuan->tahun_perolehan,
                            'sumber_dana' => $ajuan->sumber_dana,
                            'harga_perolehan' => $ajuan->harga_perolehan,
                            'cv_pengadaan' => $ajuan->cv_pengadaan,
                            'jumlah_barang' => $ajuan->jumlah,
                            'ruangan_id' => $ajuan->ruangan_id,
                            'kondisi_barang' => $ajuan->kondisi_barang ?? 'baik',
                            'kepemilikan_barang' => $ajuan->kepemilikan_barang,
                            'penanggung_jawab' => $ajuan->penanggung_jawab,
                            'gambar_barang' => $ajuan->gambar_barang,
                        ]);
                    }
                } elseif ($ajuan->tipe_pengajuan === 'baru') {
                    // Cek apakah barang dengan detail yang sama sudah ada
                    $existing = Barang::where('nama_barang', $ajuan->nama_barang)
                        ->where('merk_barang', $ajuan->merk_barang)
                        ->where('jenis_barang', $ajuan->jenis_barang)
                        ->where('ruangan_id', $ajuan->ruangan_id)
                        ->where('kondisi_barang', $ajuan->kondisi_barang ?? 'baik')
                        ->first();

                    if ($existing) {
                        // Jika ada, tambahkan jumlah
                        $existing->jumlah_barang += $ajuan->jumlah;
                        $existing->save();
                    } else {
                        // Jika tidak, buat barang baru
                        Barang::create([
                            'kode_barang' => 'BRG-' . strtoupper(Str::random(6)),
                            'kode_asal' => null,
                            'nama_barang' => $ajuan->nama_barang,
                            'jenis_barang' => $ajuan->jenis_barang,
                            'merk_barang' => $ajuan->merk_barang,
                            'tahun_perolehan' => $ajuan->tahun_perolehan,
                            'sumber_dana' => $ajuan->sumber_dana,
                            'harga_perolehan' => $ajuan->harga_perolehan,
                            'cv_pengadaan' => $ajuan->cv_pengadaan,
                            'jumlah_barang' => $ajuan->jumlah,
                            'ruangan_id' => $ajuan->ruangan_id,
                            'kondisi_barang' => $ajuan->kondisi_barang ?? 'baik',
                            'kepemilikan_barang' => $ajuan->kepemilikan_barang,
                            'penanggung_jawab' => $ajuan->penanggung_jawab,
                            'gambar_barang' => $ajuan->gambar_barang,
                        ]);
                    }
                }
            }

            $ajuan->status = $status;
            $ajuan->save();
            return redirect()->back()->with('success', 'Status pengajuan pengadaan berhasil diperbarui.');
            
        }elseif ($type === 'perawatan') {
            $ajuan = AjuanPerawatan::find($id);
            if (!$ajuan) {
                return redirect()->back()->with('error', 'Data ajuan tidak ditemukan.');
            }
            if ($status === 'Disetujui') {
                $barang = Barang::findOrFail($ajuan->perawatan->barang_id);
                if ($barang->jumlah_barang < $ajuan->perawatan->jumlah) {
                    return redirect()->back()->with('error', 'Jumlah barang tidak mencukupi untuk perawatan.');
                } else {
                    $barang->jumlah_barang -= $ajuan->perawatan->jumlah;
                    $barang->save();
                }
            }
        } elseif ($type === 'mutasi') {
            $ajuan = AjuanMutasi::find($id);
            if (!$ajuan) {
                return redirect()->back()->with('error', 'Data ajuan tidak ditemukan.');
            }
            if ($status === 'Disetujui') {
                $mutasi_id = $ajuan->mutasi;
                $mutasi = Mutasi::findOrFail($mutasi_id->id);

                $barangAsal = Barang::findOrFail($mutasi->barang_id);
                if ($mutasi->jumlah_barang > $barangAsal->jumlah_barang && $status === 'Disetujui') {
                    return redirect()->back()->with('error', 'Jumlah barang yang diminta melebihi stok tersedia.');
                }
                $barangAsal->jumlah_barang -= $mutasi->jumlah_barang;
                $barangAsal->save();

                $barangTujuan = Barang::where('nama_barang', $barangAsal->nama_barang)
                    ->where('ruangan_id', $mutasi->tujuan)
                    ->where('merk_barang', $barangAsal->merk_barang)
                    ->where('kondisi_barang', $barangAsal->kondisi_barang)
                    ->first();
                if ($barangTujuan) {
                    $barangTujuan->jumlah_barang += $mutasi->jumlah_barang;
                    $barangTujuan->save();
                } else {
                    Barang::create([
                        'kode_barang' => 'BRG-' . strtoupper(Str::random(6)),
                        'kode_asal' => $barangAsal->kode_barang . '-' . $mutasi->tujuan,
                        'nama_barang' => $barangAsal->nama_barang,
                        'jenis_barang' => $barangAsal->jenis_barang,
                        'merk_barang' => $barangAsal->merk_barang,
                        'tahun_perolehan' => $barangAsal->tahun_perolehan,
                        'sumber_dana' => $barangAsal->sumber_dana,
                        'harga_perolehan' => $barangAsal->harga_perolehan,
                        'cv_pengadaan' => $barangAsal->cv_pengadaan,
                        'jumlah_barang' => $mutasi->jumlah_barang,
                        'ruangan_id' => $mutasi->tujuan,
                        'kondisi_barang' => $barangAsal->kondisi_barang,
                        'kepemilikan_barang' => $barangAsal->kepemilikan_barang,
                        'penanggung_jawab' => $barangAsal->penanggung_jawab,
                        'gambar_barang' => $barangAsal->gambar_barang,
                    ]);
                }
            }
        } elseif ($type === 'penghapusan') {
            $ajuan = AjuanPenghapusan::find($id);
            if (!$ajuan) {
                return redirect()->back()->with('error', 'Data ajuan tidak ditemukan.');
            }
            if ($status === 'Disetujui') {
                $barang = Barang::findOrFail($ajuan->penghapusan->barang_id);
                $barang->jumlah_barang -= $ajuan->penghapusan->jumlah;
                $barang->save();
            }
        } else {
            return redirect()->back()->with('error', 'Jenis ajuan tidak valid.');
        }

        $ajuan->status = $status;
        $ajuan->save();

        return redirect()->back()->with('success', 'Status ajuan berhasil diperbarui.');
    }
}
