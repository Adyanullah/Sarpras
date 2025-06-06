<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanItem extends Model
{
    protected $guarded = ['id'];
    protected $table = 'peminjaman_items';
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
}
