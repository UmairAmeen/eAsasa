<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowedDiscountInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'allowed_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('allowed_discount')->default('100')->comment('(%)');
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
        if (Schema::hasColumn('users', 'allowed_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('allowed_discount');
            });
        }
    }
}
