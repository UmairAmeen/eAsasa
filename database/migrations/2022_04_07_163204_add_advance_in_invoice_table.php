<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvanceInInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('invoice', 'advance')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->decimal('advance', 16, 4);
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
        if (Schema::hasColumn('invoice', 'advance')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->dropColumn('advance');
            });
        }
    }
}
