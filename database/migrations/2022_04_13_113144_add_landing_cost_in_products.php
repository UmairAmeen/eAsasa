<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLandingCostInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'landing_cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->float('landing_cost', 8, 2)->default('0.00')->nullable();
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
        if (Schema::hasColumn('products', 'landing_cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('landing_cost');
            });
        }
    }
}
