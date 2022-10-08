<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFbrInvoiceInInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('invoice', 'fbr_invoice')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->string('fbr_invoice')->after('id')->nullable();
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
        if (Schema::hasColumn('invoice', 'fbr_invoice')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->dropColumn('fbr_invoice');
            });
        }
       
    }
}
