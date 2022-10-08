<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {

	protected $table = 'purchase';
	public $timestamps = true;

	public function supplier()
	{
		return $this->belongsTo('App\Supplier')->withTrashed();;
	}

	public function product()
	{
		return $this->belongsTo('App\Products')->withTrashed();
	}

	public function warehouse()
	{
		return $this->belongsTo('App\Warehouse');
	}

	public function stocks()
	{
		return $this->hasOne('App\StockManage');
	}


	// public function save(array $options = array())
	// {
	// 	$appStock = null;
	// 	if (isset($this->pid))
	// 	{
	// 		$this->id = $this->pid;
	// 		unset($this->pid);
	// 		//add a log in App\Stock
	// 		$appStock = new StockManage;
	// 		$appStock->purchase_id = $this->id;
	// 		$appStock->date = $this->date;
	// 		$appStock->type = "purchase";
	// 		$appStock->product_id = $this->product_id;
	// 		$appStock->warehouse_id = $this->warehouse_id;
	// 		$appStock->supplier_id = $this->supplier_id;
	// 		$appStock->quantity = $this->stock;
			
	// 	}else if (isset($this->id))
	// 	{
	// 		$purchase = Purchase::whereId($this->id)->first();
	// 		$current_stock = warehouse_stock($this->product, $this->warehouse_id);
	// 		$total = ($current_stock - $purchase->stock) + $this->stock;
	// 		if ($total < 0)
	// 		{
	// 			throw new Exception("Inventory can't be negative", 1);	
	// 		}
	// 		$appStock = $this->stocks;
	// 		if (!$this->supplier_id)
	// 		{
	// 			$appStock->supplier_id = null;
	// 		}else{
	// 			$appStock->supplier_id = $this->supplier_id;
	// 		}
	// 		$appStock->quantity = $this->stock;
	// 	}
	// 	parent::save();
	// 	$appStock->save();
	// }
}