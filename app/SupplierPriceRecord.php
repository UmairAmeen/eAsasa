<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierPriceRecord extends Model
{
    //
	protected $fillable = ['product_id','supplier_id'];
    public function product()
    {
    	return $this->belongsTo('App\Products')->withTrashed();
    }


    public function supplier()
    {
    	return $this->belongsTo('App\Supplier')->withTrashed();
    }
}
