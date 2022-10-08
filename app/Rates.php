<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rates extends Model {

	protected $table = 'rates';
	public $timestamps = true;
	protected $fillable = ['customer_id','product_id','salePrice'];

	public function product()
	{
		return $this->belongsTo('App\Products');
	}

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}

}