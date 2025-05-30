<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangDetail extends Model
{
    public function barang()
    {
        return $this->hasMany(Barang::class, 'barang_id');
    }
}
