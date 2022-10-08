<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFixedDiscountInUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'fixed_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('fixed_discount')->nullable();
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
        if (Schema::hasColumn('users', 'fixed_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fixed_discount');
            });
        }
    }
}
