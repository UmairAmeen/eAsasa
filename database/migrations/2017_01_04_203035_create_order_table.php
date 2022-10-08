<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderTable extends Migration {

	public function up()
	{
		Schema::create('order', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('invoice_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->float('salePrice', 8,2);
			$table->integer('quantity');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('order');
	}
}