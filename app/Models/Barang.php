<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $guarded=['id'];
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
    public function barangMaster()
    {
        return $this->belongsTo(BarangMaster::class, 'barang_id');
    }
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'barang_id');
    }
    
    public function peminjamanItem()
    {
        return $this->hasMany(PeminjamanItem::class, 'barang_id');
    }

    public function perawatanItem()
    {
        return $this->hasMany(PerawatanItem::class, 'barang_id');
    }

    public function mutasiItem()
    {
        return $this->hasMany(MutasiItem::class, 'barang_id');
    }
    public function penghapusanItem()
    {
        return $this->hasMany(PenghapusanItem::class, 'barang_id');
    }

    public function barangRusak()
    {
        return $this->hasMany(BarangRusak::class);
    }
    protected static function booted()
    {
        static::deleting(function(Barang $unit) {
            // 3) Hapus semua ajuan terkait unit ini
            $unit->mutasiItem()->delete();
            $unit->peminjamanItem()->delete();
            $unit->perawatanItem()->delete();
            $unit->penghapusanItem()->delete();
            if ($unit->barangRusak) {
                $unit->barangRusak()->delete();
            }
        });
    }
}
