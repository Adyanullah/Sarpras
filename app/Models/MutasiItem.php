<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiItem extends Model
{
    protected $guarded = ['id'];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_id');
    }
}
