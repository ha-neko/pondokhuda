<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperOwner extends Authenticatable
{
    use Notifiable;

    protected $table = 'tb_super_owner';
}
