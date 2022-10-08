<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sales_people')) {
            Schema::create('sales_people', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name',50);
                $table->string('address',100)->nullable();
                $table->string('phone', 15)->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        if (Schema::hasTable('sales_people')) {
            Schema::drop('sales_people');
        }
    }
}
