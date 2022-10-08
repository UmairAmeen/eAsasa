<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalesPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sales_people', 'commission')) {
            Schema::table('sales_people', function (Blueprint $table) {
                $table->decimal('commission', 16, 2)->nullable()->after('phone');
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
        if (Schema::hasColumn('sales_people', 'commission')) {
            Schema::table('sales_people', function (Blueprint $table) {
                $table->dropColumn('commission');
            });
        }
    }
}
