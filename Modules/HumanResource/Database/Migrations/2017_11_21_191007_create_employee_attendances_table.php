<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('employee')) {
            Schema::create('employee_attendances', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('employee_id')->unsigned();
                $table->date('day');
                $table->time('time_in');
                $table->time('time_out')->nullable();
                $table->time('overtime')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employee')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_attendances');
    }
}
