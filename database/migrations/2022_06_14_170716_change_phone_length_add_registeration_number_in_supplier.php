<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePhoneLengthAddRegisterationNumberInSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('supplier', ['phone', 'registeration_number']))
        {
            Schema::table('supplier', function (Blueprint $table) {
                $table->string('phone', 50)->change();
                $table->string('registeration_number')->nullable();
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
        if (Schema::hasColumns('supplier', ['phone', 'registeration_number'])) {
            Schema::table('supplier', function (Blueprint $table) {
                $table->dropColumn('phone');
                $table->dropColumn('registeration_number');
            });
        }
    }
}
