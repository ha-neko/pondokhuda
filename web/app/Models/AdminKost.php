<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

class AdminKost extends Model
{
    protected $table = 'tb_admin_kost';

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'kode_admin');
    }

    public function kost()
    {
        return $this->belongsTo('App\Models\Kost', 'kode_kost');
    }
}
