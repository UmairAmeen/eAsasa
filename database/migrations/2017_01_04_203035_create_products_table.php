<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('id');
			$table->string('barcode', 80)->unique()->nullable();
			$table->string('name', 50);
			$table->string('translation', 50)->nullable();
			$table->string('brand', 50)->nullable();
			$table->enum('type', array('final', 'raw'));
			$table->integer('notify');
			$table->float('salePrice', 8,2)->default('0.00');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}