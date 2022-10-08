<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model {

	protected $table = 'refund';
	public $timestamps = true;

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}

	public function supplier()
	{
		return $this->belongsTo('App\Supplier')->withTrashed();;
	}

	public function product()
	{
		return $this->belongsTo('App\Products')->withTrashed();
	}

	public function stock()
	{
		return $this->belongsTo('App\StockManage');
	}

	public function save(array $options = array())
	{
		$appStock = null;
		//new claim is added
		if (isset($this->rid))
		{
			$this->id = $this->rid;
			$warehouse_id = $this->warehouse_id;
			unset($this->warehouse_id);
			unset($this->rid);
			parent::save();

			//if claim is from customer
			if ($this->customer_id)
			{
				$appStock = new StockManage();
				$appStock->date = $this->date;
				$appStock->refund_id = $this->id;
				$appStock->warehouse_id = $warehouse_id;
				$appStock->supplier_id = $this->supplier_id;
				$appStock->quantity = $this->quantity;
				$appStock->customer_id = $this->customer_id;
				$appStock->product_id = $this->product_id;
				$appStock->type = "in";
				$appStock->save();
			}

			//add refund stock
			$appStock = new StockManage();
			$appStock->date = $this->date;
			$appStock->refund_id = $this->id;
			$appStock->warehouse_id = $warehouse_id;
			$appStock->supplier_id = $this->supplier_id;
			$appStock->quantity = $this->quantity;
			$appStock->product_id = $this->product_id;
			if ($this->customer_id)
				$appStock->customer_id = $this->customer_id;
			$appStock->type = "refund";
			$appStock->save();
			
		}

	}

	public function delete()
	{
		if ($this->stock)
			$this->stock->delete();
		parent::delete();
	}

}