<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Ajuan\{
    AjuanHandlerInterface,
    PengadaanHandler,
    PeminjamanHandler,
    PerawatanHandler,
    MutasiHandler,
    PenghapusanHandler,
    BarangRusakHandler
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind('ajuan.handler.pengadaan', PengadaanHandler::class);
        $this->app->bind('ajuan.handler.peminjaman', PeminjamanHandler::class);
        $this->app->bind('ajuan.handler.perawatan', PerawatanHandler::class);
        $this->app->bind('ajuan.handler.mutasi', MutasiHandler::class);
        $this->app->bind('ajuan.handler.penghapusan', PenghapusanHandler::class);
        $this->app->bind('ajuan.handler.barang_rusak', BarangRusakHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
