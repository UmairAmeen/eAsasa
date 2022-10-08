<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CalculateBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getpdo()->exec("DROP FUNCTION IF EXISTS `calculate_balance`;");
        DB::connection()->getpdo()->exec("CREATE DEFINER = CURRENT_USER FUNCTION `calculate_balance`(`invoice_id_detail` integer)
        RETURNS decimal(15,2)
        BEGIN
            #Routine body goes here...
        DECLARE credit DECIMAL(15,2);
        DECLARE debit DECIMAL(15,2);
        select sum(amount) into credit from `transaction` where invoice_id = invoice_id_detail AND type = 'out' AND deleted_at IS NULL;
        select sum(amount) into debit from `transaction` where invoice_id = invoice_id_detail AND type = 'in' AND deleted_at IS NULL;

            RETURN credit - debit;
        END;;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getpdo()->exec("DROP FUNCTION IF EXISTS `calculate_balance`;");
    }
}
