<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerTable extends Migration {

	public function up()
	{
		Schema::create('customer', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('phone', 15)->nullable();
			$table->string('type', 50)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('customer');
	}
}