<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $guarded=['id'];
    public function barang()
    {
        return $this->hasMany(Barang::class, 'ruangan_id'); 
    }
    public function barangAktif()
    {
        return $this->hasMany(Barang::class)->where('sedia', '>', -1);
    }
}
