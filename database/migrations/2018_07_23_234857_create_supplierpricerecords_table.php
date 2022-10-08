<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierPriceRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('supplier_price_records', function(Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('product_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->decimal('price',15,2)->default(0);
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
		Schema::drop('supplier_price_records');
	}

}
