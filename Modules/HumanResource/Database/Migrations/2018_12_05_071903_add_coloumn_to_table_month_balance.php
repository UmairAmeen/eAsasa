<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnToTableMonthBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_balance', function (Blueprint $table) {

            $table->decimal('Total_Salry',20,2)->unsigned()->default(0);
            $table->decimal('Total_release',20,2)->unsigned()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_balance', function (Blueprint $table) {

        });
    }
}
