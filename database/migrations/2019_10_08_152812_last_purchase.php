<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LastPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getpdo()->exec("CREATE DEFINER = CURRENT_USER  FUNCTION `last_purchase_price`(`product_details` integer)
 RETURNS decimal(15,2)
BEGIN
    #Routine body goes here...
    DECLARE last_price DECIMAL(15,2);
    select price into last_price from supplier_price_records where product_id = product_details ORDER BY date DESC LIMIT 1;
    RETURN last_price;
END;");
        //
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
