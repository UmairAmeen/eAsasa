<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSalaryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('employee')) {
            Schema::create('employee_salary_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('employee_id')->unsigned();
                $table->integer('salary');
                $table->integer('bonus');
                $table->integer('tax');
                $table->integer('total');
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
        Schema::dropIfExists('employee_salary_logs');
    }
}
