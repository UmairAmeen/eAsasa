<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StockManage extends Model {
	use SoftDeletes;

	protected $table = 'stocklog';
	public $timestamps = true;
	protected $fillable = ['purchase_id'];
	
	public function supplier()
	{
		return $this->belongsTo('App\Supplier')->withTrashed();
	}

	public function product()
	{
		return $this->belongsTo('App\Products','product_id')->withTrashed();
	}

	public function warehouse()
	{
		return $this->belongsTo('App\Warehouse');
	}

	public function purchase()
	{
		return $this->belongsTo('App\Purchase');
	}

	public function sale()
	{
		return $this->belongsTo('App\Order');
	}

	public function order()
	{
		return $this->belongsTo('App\Order','purchase_id');
	}

	public function refund()
	{
		return $this->belongsTo('App\Refund');
	}
	public function customer()
	{
		return $this->belongsTo('App\Customer');		
	}
	public function added_user(){
		return $this->belongsTo("App\User","added_by");
	}
	public function edited_user(){
		return $this->belongsTo("App\User","edited_by");
	}

	public function saveX(array $options = array())
	{
		if ($this->id)
		{
			parent::save();
		}

		if ($this->type == "out" || $this->type == "sale")
		{
			$getQuantity = warehouse_stock($this->product, $this->warehouse->id);
			$sub = $getQuantity - $this->quantity;
			if ($sub < 0)
			{
				throw new \Exception("Inventory Can't be below zero [".$this->product->name."(".$this->product->brand.")]", 1);
			}
		}
		parent::save();
	}
	public function deleteX()
	{
		try{
			if ($this->purchase)
				$this->purchase->delete();
			if ($this->sale)
				$this->sale->delete();
			if ($this->refund)
				$this->refund->delete();
		}catch(\Exception $e)
		{
			throw new \Exception("You cannot directly delete (Sale/Purchase/Refund) the stock log", 1);
			
		}

		$getQuantity = warehouse_stock($this->product, $this->warehouse->id);
		if ($this->type == "in")
		{

			$sub = $getQuantity - $this->quantity;
		}else{
			$sub = $getQuantity + $this->quantity;
		}
		if ($sub < 0)
		{
			throw new \Exception("Inventory Can't be below zero", 1);
		}

		parent::delete();
	}

}