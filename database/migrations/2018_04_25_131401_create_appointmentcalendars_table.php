<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentCalendarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('appointment_calendars', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('all_day')->default(false);
            $table->datetime('start');
            $table->datetime('end')->nullable();
            $table->string('background_color')->nullable();
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
		Schema::drop('appointment_calendars');
	}

}
