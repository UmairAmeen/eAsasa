<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStrnInCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('customer', ['strn', 'cnic'])) {
            Schema::table('customer', function (Blueprint $table) {
                $table->string('strn',15)->nullable();
                $table->string('cnic', 15)->change();
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
        if (Schema::hasColumns('customer', ['strn', 'cnic'])) {
            Schema::table('customer', function (Blueprint $table) {
                $table->dropColumn('strn');
                $table->dropColumn('cnic');
            });
        }
    }
}
