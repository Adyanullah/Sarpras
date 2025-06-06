<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMaster extends Model
{
    public function barang()
    {
        return $this->hasMany(Barang::class, 'barang_id');
    }
    public function pengadaan()
    {
        return $this->hasMany(Pengadaan::class, 'barang_master_id');
    }
}
