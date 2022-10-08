<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColNTNCNICInCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('customer', 'cnic'))
        {
            Schema::table('customer', function (Blueprint $table) {
                $table->string('cnic',13)->nullable()->after('name');
            });   
        }
        if (!Schema::hasColumn('customer', 'ntn'))
        {
            Schema::table('customer', function (Blueprint $table) {
                $table->string('ntn',9)->nullable()->after('cnic');
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
        if (Schema::hasColumn('customer', 'cnic')) {
            Schema::table('customer', function (Blueprint $table) {
                $table->dropColumn('cnic');
            });
                   
        }
        if (Schema::hasColumn('customer', 'ntn')) {
            Schema::table('customer', function (Blueprint $table) {
                $table->dropColumn('ntn');
            });
                   
        }
    }
}
