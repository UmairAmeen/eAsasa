<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AltTbStocklogModifyColumnQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `stocklog` MODIFY COLUMN `quantity`  decimal(16,3)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `stocklog` MODIFY COLUMN `quantity`  decimal(5,3)");
    }
}
