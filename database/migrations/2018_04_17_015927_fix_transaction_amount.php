<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTransactionAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('transaction', function (Blueprint $table) {
        //     //
        //     $table->double('amount',20,2)->change();
        // });
        DB::statement("ALTER TABLE `transaction` MODIFY COLUMN `amount`  double(20,2) NOT NULL AFTER `transaction_id`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('transaction', function (Blueprint $table) {
        //     //
        //     $table->double('amount',8,2)->change();
        // });
        DB::statement("ALTER TABLE `transaction` MODIFY COLUMN `amount`  double(8,2) NOT NULL AFTER `transaction_id`;");
    }
}
