<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ColoumnsToEmplyePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('employee_payment', function (Blueprint $table) {
            $table->decimal('payment_release',20,2)->unsigned()->default(0);
            $table->decimal('payment_remaining',20,2)->unsigned()->default(0);
            $table->decimal('payment_advance',20,2)->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
