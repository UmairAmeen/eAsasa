<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model {

	use SoftDeletes;

	protected $table = 'customer';
	public $timestamps = true;
	protected $fillable = ['supplier_id'];

	public function rate()
	{
		return $this->hasMany('App\Rates');
	}

	public function transactions()
	{
		return $this->hasMany('App\Transaction');
	}

	public function invoice()
	{
		return $this->hasMany('App\Invoice');
	}

	public function stocks()
	{
		return $this->hasMany('App\StockManage');
	}
	public function refund()
	{
		return $this->hasMany('App\Refund');
	}
	public function added_user(){
		return $this->belongsTo("App\User","added_by");
	}
	public function edited_user(){
		return $this->belongsTo("App\User","edited_by");
	}

}