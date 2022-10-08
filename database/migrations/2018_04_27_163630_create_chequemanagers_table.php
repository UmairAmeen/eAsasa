<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChequeManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cheque_managers', function(Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            //[cheque] => bank(optional), amount, check no(optional), release date(optional), party(optional*)
            $table->string('bank')->nullable();
            $table->enum('type',['in','out']);
            $table->decimal('amount',15,2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('customer_id')->unsigned()->nullable();
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
		Schema::drop('cheque_managers');
	}

}
