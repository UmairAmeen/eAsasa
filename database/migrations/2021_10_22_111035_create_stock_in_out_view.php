<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockInOutView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('stock_in_out_view')) {
            DB::connection()->getpdo()->exec("CREATE VIEW `stock_in_out_view` AS
            SELECT distinct(p.id) ,p.name,p.brand,p.itemcode,
            (Select sum(quantity) from stocklog where product_id = p.id and `deleted_at` is null and `type` in ('in' , 'purchase')) as stockIn,
            (Select sum(quantity) from stocklog where product_id = p.id and `deleted_at` is null and `type` in ('out' , 'sale')) as stockOut
            from `stocklog` as l
            join `products` as p on p.id = l.product_id
            GROUP BY p.id");
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getpdo()->exec("DROP VIEW `stock_in_out_view`;");
    }
}
