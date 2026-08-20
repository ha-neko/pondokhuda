<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

class Kost extends Model
{
    protected $table = 'tb_kost';

    protected $primaryKey = 'kode_kost';

    public function ownerkost()
    {
        return $this->hasMany('App\Models\OwnerKost', 'kode_kost');
    }

    public function adminkost()
    {
        return $this->hasMany('App\Models\AdminKost', 'kode_kost');
    }

    public function penyewa()
    {
        return $this->hasOne('App\Models\Penyewa', 'kode_kost');
    }
}
