<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePurchaseTable extends Migration {

	public function up()
	{
		Schema::create('purchase', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->integer('warehouse_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('supplier_id')->unsigned()->nullable();
			$table->float('price', 8,2);
			$table->integer('stock')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('purchase');
	}
}