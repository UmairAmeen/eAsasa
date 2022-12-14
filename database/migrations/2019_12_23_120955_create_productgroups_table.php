<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_groups', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal("price",10,5);
            $table->text("products")->nullable();
            $table->text("quantity")->nullable();
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_groups');
	}

}
