<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Owner extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    protected $table = 'tb_owner';

    protected $fillable = [
    	'kode',
    ];

    public $timestamps = false;
    
    public $incrementing=false;

    protected $primaryKey = 'kode';

    /** Legacy auth tables do not have Laravel's remember_token column. */
    public function getRememberTokenName()
    {
        return null;
    }

    public function ownerkost()
    {
        return $this->hasMany('App\Models\OwnerKost', 'kode_owner');
    }
}
