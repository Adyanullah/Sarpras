<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanItem extends Model
{
    protected $fillable = ['pengadaan_id','ruangan_id','jumlah'];

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
