<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColPCTCodeTaxRateInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'pct_code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('pct_code',8)->nullable()->after('brand');
            });
        }
        if (!Schema::hasColumn('products', 'tax_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->float('tax_rate',17)->nullable()->after('pct_code');
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
        if (Schema::hasColumn('products', 'pct_code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('pct_code');
            });
                   
        }
        if (Schema::hasColumn('products', 'tax_rate')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('tax_rate');
            });
                   
        }
    }
}
