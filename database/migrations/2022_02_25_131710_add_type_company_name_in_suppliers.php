<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeCompanyNameInSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('supplier', 'type')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->string('type')->nullable();
            });
        }
        if (!Schema::hasColumn('supplier', 'company_name')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->string('company_name')->nullable();
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
        if (Schema::hasColumn('supplier', 'type')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('supplier', 'company_name')) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropColumn('company_name');
            });
        }
    }
}
