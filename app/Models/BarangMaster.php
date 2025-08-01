<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMaster extends Model
{
    protected $guarded = ['id'];
    public function barang()
    {
        return $this->hasMany(Barang::class, 'barang_id');
    }
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'barang_master_id');
    }

    protected static function booted()
    {
        static::deleting(function(BarangMaster $master) {
            // Hapus semua pengadaan header + detail items
            foreach ($master->pengadaan()->get() as $ajuan) {
                $ajuan->items()->delete();
                $ajuan->delete();
            }
            // Hapus semua unit (akan trigger Barang::deleting)
            foreach ($master->barang()->get() as $unit) {
                $unit->delete();
            }
        });
    }
}
