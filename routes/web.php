<?php

use App\Http\Controllers\AjuanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DataRuanganController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PerawatanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenghapusanController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\BarangRuangController;
use App\Models\Perawatan;
// use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'Authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/barangRusak', [BarangRusakController::class, 'index'])->name('barangRusak.index');
    Route::get('/barangMasuk', [BarangController::class, 'barangMasuk'])->name('barang.masuk');

    // Kelola Barang
    Route::get('/inventaris', [BarangController::class, 'index'])->middleware('role:1,2,3,4')->name('inventaris.index');
    Route::get('/inventaris/unit/{id}', [BarangController::class, 'unit'])->name('inventaris.unit');
    Route::get('/inventaris/unit/{id}/detail', [BarangController::class, 'show'])->name('inventaris.detail');
    Route::post('/inventaris/unit/{id}/rusak', [BarangController::class, 'barangRusak'])->name('inventaris.rusak');
    Route::post('/inventaris/unit/aksi', [BarangController::class, 'aksi'])->name('inventaris.aksi');
    Route::put('/inventaris/detail/{id}/edit', [BarangController::class, 'update'])->name('inventaris.update');
    Route::put('/inventaris/{id}/edit', [BarangController::class, 'updateMaster'])->name('inventaris.update.master');

    Route::get('/inventaris/qr-cetak/{ukuran}', [BarangController::class, 'cetakQR'])->name('inventaris.qr.cetak');
    Route::get('/barang/scan/result', [BarangController::class, 'scanResult']);
    Route::view('/scan', 'inventaris.scan');
    Route::get('/qr', function () {
        return view('inventaris.qr');
    });

    // Ruangan
    Route::get('/ruang', [DataRuanganController::class, 'index'])->middleware('role:1');
    Route::post('/ruang/tambah', [DataRuanganController::class, 'store']);
    Route::post('/ruang/ubah/{id}', [DataRuanganController::class, 'edit']);

    //pengadaan
    Route::get('/pengadaan', [PengadaanController::class, 'index'])->middleware('role:1,3');
    // Route::get('/pengajuan/tambah-jumlah', [PengadaanController::class, 'createTambahJumlah'])->name('barang-requests.tambah-jumlah');
    // Route::get('/pengajuan/tambah-baru', [PengadaanController::class, 'createTambahBaru'])->name('barang-requests.tambah-baru');
    Route::post('/pengajuan/store', [PengadaanController::class, 'store'])->name('pengadaan.store');
    Route::put('/pengadaan/update/{id}', [PengadaanController::class, 'update'])->name('pengadaan.update');
    Route::delete('/pengadaan/{id}', [PengadaanController::class, 'destroy'])->name('pengadaan.destroy');

    // Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->middleware('role:1,3');
    Route::post('/peminjaman/tambah', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'checkItem'])->name('peminjaman.checkItem');
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::put('/peminjaman/{id}/updateStatus', [PeminjamanController::class, 'updateStatus'])->name('peminjaman.updateStatus');
    Route::get('/laporan/peminjaman/pdf', [PeminjamanController::class, 'cetakPDF'])->name('peminjaman.cetakPDF');
    Route::get('/laporan/peminjaman/excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.exportExcel');

    // Ajuan
    // Route::get('/verifikasiAjuan', [AjuanController::class, 'index'])->middleware('role:1,2');
    // Route::put('/verifikasiAjuan/update/{type}/{id}/{status}', [AjuanController::class, 'UpdateStatus'])->name('ajuan.updateStatus');
    
    Route::get('/verifikasiAjuan', [AjuanController::class, 'index'])->name('waka.verifikasi.index');
    Route::put('/verifikasiAjuan/{type}/{id}/{status}', 
                [AjuanController::class, 'updateStatus'])
         ->name('ajuan.updateStatus');

    // Perawatan
    Route::get('/perawatan', [PerawatanController::class, 'index'])->middleware('role:1,3');
    Route::post('/perawatan', [PerawatanController::class, 'store'])->name('perawatan.store');
    Route::put('/perawatan/{id}', [PerawatanController::class, 'update'])->name('perawatan.update');
    Route::delete('/perawatan/{id}', [PerawatanController::class, 'destroy'])->name('perawatan.destroy');
    Route::put('/perawatan/updateStatus/{id}', [PerawatanController::class, 'updateStatus'])->name('perawatan.updateStatus');


    // Mutasi
    Route::get('/mutasi', [MutasiController::class, 'index'])->middleware('role:1,3')->name('mutasi.index');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
    Route::put('/mutasi', [MutasiController::class, 'updateStatus'])->name('mutasi.updateStatus');
    Route::put('/mutasi/{id}', [MutasiController::class, 'update'])->name('mutasi.update');
    Route::delete('/mutasi/{id}', [MutasiController::class, 'destroy'])->name('mutasi.destroy');

    // Penghapusan
    Route::get('/penghapusan', [PenghapusanController::class, 'index'])->middleware('role:1,3');
    Route::put('/penghapusan/{id}/update', [PenghapusanController::class, 'update'])->name('penghapusan.update');
    Route::delete('/penghapusan/{id}', [PenghapusanController::class, 'destroy'])->name('penghapusan.destroy');

    // Laporan
    Route::get('/laporan/pengadaan', [PengadaanController::class, 'laporan'])->middleware('role:1,2,4')->name('pengadaan.laporan');
    Route::get('/laporan/perawatan', [PerawatanController::class, 'laporan'])->middleware('role:1,2,4')->middleware('role:1,2,4')->name('perawatan.laporan');
    Route::get('/laporan/peminjaman', [PeminjamanController::class, 'laporan'])->middleware('role:1,2,4')->name('peminjaman.laporan');
    Route::get('/laporan/mutasi', [MutasiController::class, 'laporan'])->middleware('role:1,2,4')->name('mutasi.laporan');
    Route::get('/laporan/penghapusan', [PenghapusanController::class, 'laporan'])->name('penghapusan.laporan');

    Route::get('/laporan/perawatan/pdf/{bulan}', [PerawatanController::class, 'exportPDF']);
    Route::get('/laporan/perawatan/excel/{bulan}', [PerawatanController::class, 'exportExcel']);

    Route::get('/laporan/peminjaman/pdf/{bulan}', [PeminjamanController::class, 'exportPDF'])->name('peminjaman.pdf');
    Route::get('/laporan/peminjaman/excel/{bulan}', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.excel');

    Route::get('/laporan/mutasi/pdf/{bulan}', [MutasiController::class, 'exportPDF'])->name('mutasi.pdf');
    Route::get('/laporan/mutasi/excel/{bulan}', [MutasiController::class, 'exportExcel'])->name('mutasi.excel');

    Route::get('/laporan/penghapusan/pdf/{bulan}', [PenghapusanController::class, 'exportPDF'])->name('penghapusan.pdf');
    Route::get('/laporan/penghapusan/excel/{bulan}', [PenghapusanController::class, 'exportExcel'])->name('penghapusan.excel');

    Route::get('/laporan/pengadaan/pdf/{bulan}', [PengadaanController::class, 'exportPDF'])->name('pengadaan.pdf');
    Route::get('/laporan/pengadaan/excel/{bulan}', [PengadaanController::class, 'exportExcel'])->name('pengadaan.excel');

    Route::get('/laporan/barangruang', [BarangRuangController::class, 'index'])->name('ruangan.index');
    Route::get('/laporan-barang-ruang/{id}', [BarangRuangController::class, 'detail'])->name('ruangan.detail');

    // Pengaturan
    Route::get('/pengaturan/ruangan', [DataRuanganController::class, 'index'])->middleware('role:1');
    Route::get('/pengaturan/user', [UserController::class, 'index'])->middleware('role:1');
    Route::delete('/pengaturan/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/pengaturan/user/tambah', [UserController::class, 'store'])->name('user.store');
    Route::put('/pengaturan/user/{id}', [UserController::class, 'update'])->name('user.update');

    //user
    Route::get('/ganti-password', [UserController::class, 'editPassword'])->name('user.password.edit');
    Route::post('/ganti-password', [UserController::class, 'updatePassword'])->name('user.password.update');

    

    // Route::post('/inventaris/tambah', [BarangController::class, 'store'])->name('inventaris.store');
    // Route::delete('/inventaris/hapus/{id}', [BarangController::class, 'destroy'])->name('inventaris.destroy');
    // Route::delete('/inventaris/ajukanHapus/{id}', [BarangController::class, 'destroyApp'])->name('inventaris.destroy.app');

    // Route::post('/inventaris/pengadaan', [BarangController::class, 'pengadaan'])->name('inventaris.pengadaan');
    // Route::post('/inventaris/pengadaan/baru', [BarangController::class, 'pengadaanBaru'])->name('inventaris.pengadaan.baru');
});
