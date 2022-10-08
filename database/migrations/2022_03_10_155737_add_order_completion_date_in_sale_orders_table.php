<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderCompletionDateInSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sale_orders', 'completion_date')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                $table->date('completion_date')->nullable();
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
        if (Schema::hasColumn('sale_orders', 'completion_date')) {
            Schema::table('sale_orders', function (Blueprint $table) {
                $table->dropColumn('completion_date');
            });
        }
    }
}
