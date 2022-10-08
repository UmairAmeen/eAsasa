<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model {

	protected $table = 'products';
	public $timestamps = true;
	protected $fillable = ['id','name','brand','salePrice','category_id','itemcode','pct_code','tax_rate','landing_cost'];

	use SoftDeletes;

	protected $dates = ['deleted_at'];

	public function purchase()
	{
		return $this->hasMany('App\Purchase','product_id');
	}

	public function refund()
	{
		return $this->hasMany('App\Refund','product_id');
	}

	public function order()
	{
		return $this->hasMany('App\Order','product_id');
	}
	public function stock()
	{
		return $this->hasMany('App\StockManage','product_id');
	}

	public function unit()
	{
		return $this->belongsTo('App\Unit');
	}
	public function added_user(){
		return $this->belongsTo("App\User","added_by");
	}
	public function edited_user(){
		return $this->belongsTo("App\User","edited_by");
	}

	public function category()
	{
		return $this->belongsTo("App\ProductCategory", "category_id")->withTrashed();
	}
	// public function delete()
	// {
	// 	// $this->stock()->delete();
	// 	// $this->purchase()->delete();
	// 	// $this->order()->delete();
	// 	// $this->refund()->delete();
	// 	// parent::delete();
	// }

}