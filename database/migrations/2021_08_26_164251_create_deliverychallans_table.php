<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryChallansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_challans', function(Blueprint $table) {
            $table->increments('id');
            $table->string('rep_by')->nullable();
            $table->date('date');
            $table->string('order_no')->nullable();
            $table->integer('customer_id');
            $table->text('address')->nullable();
            $table->string('o_details')->nullable();
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
		Schema::drop('delivery_challans');
	}

}
