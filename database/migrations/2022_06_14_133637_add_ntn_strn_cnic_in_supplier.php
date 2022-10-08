<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNtnStrnCnicInSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('supplier', ['cnic', 'ntn', 'strn']))
        {
            Schema::table('supplier', function (Blueprint $table) {
                $table->string('cnic',15)->nullable();
                $table->string('ntn',9)->nullable();
                $table->string('strn',15)->nullable();
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
        if (Schema::hasColumns('supplier', ['cnic', 'ntn', 'strn'])) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropColumn('cnic');
                $table->dropColumn('ntn');
                $table->dropColumn('strn');
            });
                   
        }
    }
}
