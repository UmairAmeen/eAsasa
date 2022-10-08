<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionTable extends Migration {

	public function up()
	{
		Schema::create('transaction', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->enum('type', array('in', 'out'));
			$table->integer('invoice_id')->unsigned()->nullable();
			$table->string('bank', 50)->nullable();
			$table->string('transaction_id', 20)->nullable();
			$table->float('amount', 8,2);
			$table->date('release_date')->nullable();
			$table->enum('payment_type', array('cash', 'cheque', 'transfer'));
			$table->integer('customer_id')->unsigned()->nullable();
			$table->integer('supplier_id')->unsigned()->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('transaction');
	}
}