<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankIdInInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('invoice', 'bank_id')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->integer('bank_id')->unsigned()->nullable();
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
        if (Schema::hasColumn('invoice', 'bank_id')) {
            Schema::table('invoice', function (Blueprint $table) {
                $table->dropColumn('bank_id');
            });
        }
    }
}
