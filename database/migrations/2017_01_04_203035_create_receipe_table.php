<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReceipeTable extends Migration {

	public function up()
	{
		Schema::create('receipe', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('raw_id')->unsigned();
			$table->integer('final_id')->unsigned();
			$table->integer('quantity');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('receipe');
	}
}