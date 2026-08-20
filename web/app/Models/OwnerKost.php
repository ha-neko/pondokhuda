<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

class OwnerKost extends Model
{
    protected $table = 'tb_owner_kost';

    public function owner()
    {
        return $this->belongsTo('App\Models\Owner', 'kode_owner');
    }

    public function kost()
    {
        return $this->belongsTo('App\Models\Kost', 'kode_kost');
    }
}
