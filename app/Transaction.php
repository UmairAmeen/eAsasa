<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {

	use SoftDeletes;
	protected $table = 'transaction';
	public $timestamps = true;
	protected $fillable = ['type','invoice_id'];

	public function invoice()
	{
		return $this->belongsTo('App\Invoice');
	}

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}

	public function supplier()
	{
		return $this->belongsTo('App\Supplier')->withTrashed();;
	}

	public function added_user(){
		return $this->belongsTo("App\User","added_by");
	}

	public function expense(){
		return $this->belongsTo("App\ExpenseHead","expense_head");
	}
	
	public function edited_user(){
		return $this->belongsTo("App\User","edited_by");
	}

	public function bank_detail() {
		return $this->belongsTo('App\BankAccount', 'bank');
	}

	public static $types = [
		'cash' =>'Cash',
		'cheque' => 'Cheque',
		'transfer' => 'Online Transfer'
	];

}