<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDeliveryChallanInStocklog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocklog', function (Blueprint $table) {
            $table->integer('delivery_ChallanNo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocklog', function (Blueprint $table) {
            $table->dropColumn('delivery_ChallanNo');
        });
    }
}
