<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('employee')) {
            Schema::create('employee_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('employee_id')->unsigned();
                $table->string('title');
                $table->string('description');
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
        Schema::dropIfExists('employee_notes');
    }
}
