<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SaleOrder extends Model
{
        use SoftDeletes;

	const PENDING = 0;
	const ACTIVE = 1;
	const QUOTATION = 3;
	const COMPLETED = 4;
    const FINISHED = 5;
    //

    public function invoice()
    {
    	return $this->belongsTo('App\Invoice');
    }

    public function customer()
    {
    	return $this->belongsTo('App\Customer');
    }

    public function stock()
    {
        return $this->hasMany('App\StockManage','sale_orders_id');
    }

    public function saleOrder_person()
    {
    	return $this->belongsTo('App\SalesPerson', 'sales_people_id');
    }
}
