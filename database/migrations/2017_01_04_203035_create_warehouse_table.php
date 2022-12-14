<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWarehouseTable extends Migration {

	public function up()
	{
		Schema::create('warehouse', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('address', 50)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('warehouse');
	}
}