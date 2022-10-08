<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesPerson extends Model
{
    use SoftDeletes;
    protected $table = 'sales_people';
    protected $fillable = ['name','phone'];
    public $timestamps = true;

    public function salePerson_order()
    {
    	return $this->hasMany('App\SaleOrder','sales_people_id','id');
    }
}
