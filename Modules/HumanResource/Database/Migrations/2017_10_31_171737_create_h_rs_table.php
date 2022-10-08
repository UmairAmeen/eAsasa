<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHRsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('father_name');
            $table->string('picture')->nullable();
            $table->biginteger('cnic');
            $table->string('cnic_front')->nullable();
            $table->string('cnic_back')->nullable();
            $table->string('address');
            $table->biginteger('phone');
            $table->biginteger('phone_office');
            $table->string('position');
            $table->enum('status', ['Active', 'In-Active']);
            $table->enum('type', ['Daily Wage', 'Contract', 'Salary']);
            $table->biginteger('salary');
            $table->date('date_of_joining');
            $table->date('date_of_leaving')->nullable();
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
        Schema::dropIfExists('employee');
    }
}
