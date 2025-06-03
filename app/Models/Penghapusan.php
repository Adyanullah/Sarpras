<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghapusan extends Model
{
    
    protected $guarded = ['id'];
    protected $table = 'penghapusan';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function penghapusanItem()
    {
        return $this->hasMany(PenghapusanItem::class);
    }
}
