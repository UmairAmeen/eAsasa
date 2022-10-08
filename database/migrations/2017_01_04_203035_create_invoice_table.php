<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceTable extends Migration {

	public function up()
	{
		Schema::create('invoice', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('customer_id')->unsigned()->nullable();
			$table->text('description')->nullable();
			$table->float('shipping', 8,2)->nullable();
			$table->float('tax', 8,2)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('invoice');
	}
}