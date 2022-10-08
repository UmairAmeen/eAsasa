<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model {

	use SoftDeletes;

	protected $table = 'supplier';
	protected $fillable = ['name'];
	public $timestamps = true;

	public function purchase()
	{
		return $this->hasMany('App\Purchase');
	}

	public function stocks()
	{
		return $this->hasMany('App\StockManage');
	}
	public function transactions()
	{
		return $this->hasMany('App\Transaction');
	}


	public function invoices()
	{
		return $this->hasMany('App\Invoice');
	}

	// public function delete()
	// {
	// 	// if (count($this->stocks) > 0 || count($this->purchase) > 0)
	// 	// {
	// 	// 	throw new \Exception("You cannot delete a supplier with purchase/stock data", 1);
	// 	// }
	// 	// parent::delete();
	// }

}