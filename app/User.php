<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'allowed_discount','fixed_discount','master_discount','allowed_discount_pkr'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function photo()
    {
        // print_r(public_path().'/images' . $this->id . '_' . $this->name . '.jpg');
        if(file_exists( public_path().'/images/' . $this->id . '_' . $this->name . '.jpg')) {
            return '/images/' . $this->id . '_' . $this->name . '.jpg';
        } else {
            return '/assets/img/asasa.jpg';
        }     
    }
}
