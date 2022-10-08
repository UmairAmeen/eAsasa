<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
	use SoftDeletes;

	protected $table = 'invoice';
	public $timestamps = true;

	public function transactions()
	{
		return $this->hasMany('App\Transaction');
	}

	public function deliveries()
	{
		return $this->hasMany('App\DeliveryChallan','order_no');
	}

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}
	public function supplier()
	{
		return $this->belongsTo('App\Supplier');
	}

	public function orders()
	{
		return $this->hasMany('App\Order');
	}

	public function trashed_orders()
	{
		return $this->hasMany('App\Order')->withTrashed();
	}

	public function sale_order()
	{
		return $this->hasOne('App\SaleOrder');
	}

	public function trashed_sale_order()
	{
		return $this->hasOne('App\SaleOrder')->withTrashed();
	}

	public function firstOrder()
	{
		return $this->hasOne('App\Order');
	}

	public function added_user(){
		return $this->belongsTo("App\User","added_by");
	}
	public function edited_user(){
		return $this->belongsTo("App\User","edited_by");
	}

	public function bank(){
		return $this->belongsTo("App\BankAccount");
	}

	public static function GetSaleTaxPercentage() {
		$tax = getSetting('tax_percentage');
		if(is_nan($tax)) {
			return 0;
		}
		return round($tax/100, 2);
	}

}