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
    
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }

    public function perawatan()
    {
        return $this->hasMany(Perawatan::class, 'barang_id');
    }

    public function mutasi()
    {
        return $this->hasMany(Mutasi::class, 'barang_id');
    }
    public function penghapusan()
    {
        return $this->hasMany(Penghapusan::class, 'barang_id');
    }
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'barang_id');
    }
}
