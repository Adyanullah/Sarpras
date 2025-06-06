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
}
