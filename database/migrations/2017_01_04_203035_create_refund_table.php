<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRefundTable extends Migration {

	public function up()
	{
		Schema::create('refund', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->integer('customer_id')->unsigned()->nullable();
			$table->integer('product_id')->unsigned();
			$table->integer('supplier_id')->unsigned()->nullable();
			$table->integer('quantity');
			$table->integer('price');
			$table->text('description')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('refund');
	}
}