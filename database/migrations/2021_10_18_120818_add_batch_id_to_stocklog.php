<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchIdToStocklog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('stocklog')) {
            Schema::table('stocklog', function (Blueprint $table) {
                $table->integer('batch_id')->unsigned()->nullable();
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
        Schema::table('stocklog', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
    }
}
