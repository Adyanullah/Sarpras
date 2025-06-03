<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatanItem extends Model
{
    protected $guarded = ['id'];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function perawatan()
    {
        return $this->belongsTo(Perawatan::class, 'perawatan_id');
    }
}
