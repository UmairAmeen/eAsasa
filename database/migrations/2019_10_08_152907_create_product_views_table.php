<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getpdo()->exec("CREATE 
VIEW `productview`AS 
SELECT
products.id,
products.barcode,
products.`name`,
products.translation,
products.brand,
products.type,
products.notify,
products.salePrice,
products.deleted_at,
products.created_at,
products.updated_at,
products.unit_id,
units.`name` as unit_name,
calculate_stock(products.id) as stock,
last_purchase_price(products.id) as purchase_price
FROM
products
INNER JOIN units ON units.id = products.unit_id
WHERE
products.deleted_at IS NULL
GROUP BY
products.id,
products.barcode,
products.`name`,
products.translation,
products.brand,
products.type,
products.notify,
products.salePrice,
products.deleted_at,
products.created_at,
products.updated_at,
products.unit_id,
units.`name` ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_views');
    }
}
