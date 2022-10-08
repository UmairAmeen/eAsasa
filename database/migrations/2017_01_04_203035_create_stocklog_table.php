<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStocklogTable extends Migration {

	public function up()
	{
		Schema::create('stocklog', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->enum('type', array('in', 'out', 'purchase', 'sale', 'refund'));
			$table->integer('customer_id')->unsigned()->nullable();
			$table->integer('purchase_id')->unsigned()->nullable();
			$table->integer('sale_id')->unsigned()->nullable();
			$table->integer('refund_id')->unsigned()->nullable();
			$table->integer('product_id')->unsigned();
			$table->integer('warehouse_id')->unsigned();
			$table->integer('supplier_id')->unsigned()->nullable();
			$table->integer('quantity')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('stocklog');
	}
}