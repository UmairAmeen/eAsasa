<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PriceRecord extends Model
{
    //
    use SoftDeletes;
    // protected $fillable = ['date','product_id'];
    protected $fillable = ['date', 'product_id'];

    public function product()
    {
    	return $this->belongsTo('App\Products');
    }

}
