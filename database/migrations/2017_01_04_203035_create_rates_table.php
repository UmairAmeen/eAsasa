<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRatesTable extends Migration {

	public function up()
	{
		Schema::create('rates', function(Blueprint $table) {
			$table->increments('id');
			$table->float('salePrice', 8,2);
			$table->integer('product_id')->unsigned();
			$table->integer('customer_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('rates');
	}
}