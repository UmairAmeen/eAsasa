<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawReceipeRule extends Model {

	protected $table = 'receipe';
	public $timestamps = true;

	public function raw()
	{
		return $this->belongsTo('App\Products', 'raw_id');
	}

	public function final_product()
	{
		return $this->hasOne('App\Products', 'final_id');
	}

}