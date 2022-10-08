<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMasterDiscountInUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'master_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('master_discount')->nullable();
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
        if (Schema::hasColumn('users', 'master_discount')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('master_discount');
            });
        }
    }
}
