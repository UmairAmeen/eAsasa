<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('inventory_view')) {
            DB::connection()->getpdo()->exec("CREATE VIEW `inventory_view` AS
                SELECT distinct(r.id) as batch_id,
                p.id,
                p.name,
                p.brand,
                p.color,
                p.size,
                p.pattern,
                r.price,
                l.warehouse_id,
                r.date as purchase_date,
                r.supplier_id,
                (Select sum(quantity) from stocklog where product_id = l.product_id and batch_id = l.batch_id and `type` in ('in' , 'purchase')) as stockIn,
                (Select sum(quantity) from stocklog where product_id = l.product_id and batch_id = l.batch_id and `type` in ('out' , 'sale')) as stockOut
            from `stocklog` as l
            join `supplier_price_records` as r on l.product_id = r.product_id and l.supplier_id = r.supplier_id and r.id = l.batch_id
            join `products` as p on p.id = l.product_id");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getpdo()->exec("DROP VIEW `inventory_view`;");
    }
}
