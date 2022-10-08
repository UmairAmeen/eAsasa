<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Unit;

class CreateUnitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('units', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('deleteable')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
        $po = new Unit();
        $po->name = "pcs";
        $po->id = 1;
        $po->deleteable = false;
        $po->save();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('units');
	}

}
