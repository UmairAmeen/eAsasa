<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Warehouse extends Model {
	use SoftDeletes;

	protected $table = 'warehouse';
	public $timestamps = true;

	public function stocks()
	{
		return $this->hasMany('App\StockManage');
	}

}