<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSupplierTable extends Migration {

	public function up()
	{
		Schema::create('supplier', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->string('phone', 15)->nullable();
			$table->string('address', 60)->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('supplier');
	}
}