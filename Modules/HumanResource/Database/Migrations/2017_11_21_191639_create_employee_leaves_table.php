<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('employee')) {
            Schema::create('employee_leaves', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('employee_id')->unsigned();
                $table->date('day');
                $table->enum('type', ['Full', 'Half']);
                $table->string('leave_type')->nullable();
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
        Schema::dropIfExists('employee_leaves');
    }
}
