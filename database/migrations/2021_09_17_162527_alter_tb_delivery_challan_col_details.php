<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTbDeliveryChallanColDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->text('o_details')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->string('o_details', 255)->change('address');
        });
    }
}
