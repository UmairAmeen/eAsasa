<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowedDiscountPkrInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'allowed_discount_pkr')) {
            Schema::table('users', function (Blueprint $table) {
                $table->float('allowed_discount_pkr')->default(999999999999);
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
        if (Schema::hasColumn('users', 'allowed_discount_pkr')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('allowed_discount_pkr');
            });
        }
    }
}
