<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->decimal('price',12,2);
            $table->date('date');
            $table->string('type')->default('purchase');
            $table->softDeletes();
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
        Schema::drop('price_records');
    }
}
