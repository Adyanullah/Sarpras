<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $guarded = ['id'];
    protected $table = 'peminjaman';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function peminjamanItem()
    {
        return $this->hasMany(PeminjamanItem::class, 'peminjaman_id');
    }
}
