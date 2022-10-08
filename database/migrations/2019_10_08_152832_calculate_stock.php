<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CalculateStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getpdo()->exec("CREATE DEFINER = CURRENT_USER FUNCTION `calculate_stock`(`product_id_detail` integer)
 RETURNS decimal(15,2)
BEGIN
    #Routine body goes here...
DECLARE in_stock DECIMAL(15,2);
DECLARE out_stock DECIMAL(15,2);

select sum(quantity) into in_stock from stocklog where product_id = product_id_detail and type IN ('in','purchase') AND deleted_at IS NULL;
select sum(quantity) into out_stock from stocklog where product_id = product_id_detail and type IN ('out','sale') AND deleted_at IS NULL;

    RETURN in_stock - out_stock;
END;;");
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
