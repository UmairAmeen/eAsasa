<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSchemaForDecimalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `transaction` MODIFY COLUMN `amount` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `stocklog` MODIFY COLUMN `quantity` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `rates` MODIFY COLUMN `salePrice` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `purchase` MODIFY COLUMN `price` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `product_groups` MODIFY COLUMN `price` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `price_records` MODIFY COLUMN `price` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `employee_bonuses` MODIFY COLUMN `bonus` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `order` MODIFY COLUMN `quantity` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `order` MODIFY COLUMN `salePrice` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `refund` MODIFY COLUMN `quantity` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `refund` MODIFY COLUMN `price` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `salePrice` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `min_sale_price` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `employee_payment` MODIFY COLUMN `payment` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `employee_payment` MODIFY COLUMN `payment_release` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `employee_payment` MODIFY COLUMN `payment_remaining` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `employee_payment` MODIFY COLUMN `payment_advance` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `monthly_balance` MODIFY COLUMN `payment_remaining` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `monthly_balance` MODIFY COLUMN `payment_advance` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `monthly_balance` MODIFY COLUMN `Total_Salry` DECIMAL(20, 4)");
        // DB::statement("ALTER TABLE `monthly_balance` MODIFY COLUMN `Total_release` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `invoice` MODIFY COLUMN `shipping` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `invoice` MODIFY COLUMN `tax` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `invoice` MODIFY COLUMN `discount` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `invoice` MODIFY COLUMN `total` DECIMAL(20, 4)");
        DB::statement("ALTER TABLE `invoice` MODIFY COLUMN `tax_percentage` DECIMAL(20, 4)");
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
