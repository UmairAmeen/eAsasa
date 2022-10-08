<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesPeopleIdInSaleOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sale_orders', 'sales_people_id')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                $table->integer('sales_people_id')->unsigned()->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sale_orders', 'sales_people_id')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                $table->dropColumn('sales_people_id');
            });
        }
    }
}
