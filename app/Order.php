<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {
	use SoftDeletes;

	protected $table = 'order';
	public $timestamps = true;

	public function invoice()
	{
		return $this->belongsTo('App\Invoice');
	}

	public function product()
	{
		return $this->belongsTo('App\Products')->withTrashed();
	}

	public function stocks()
	{
		return $this->hasOne('App\StockManage','sale_id');
	}

	public function stock_purchase()
	{
		return $this->hasOne('App\StockManage','purchase_id');
	}

	// public function 

}