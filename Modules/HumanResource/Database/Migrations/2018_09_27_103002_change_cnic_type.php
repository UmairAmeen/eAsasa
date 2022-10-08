<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCnicType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        DB::select("ALTER TABLE `employee` CHANGE `cnic` `cnic` VARCHAR(16) NOT NULL");

        // Schema::table('employee', function (Blueprint $table) {
        
        //     $table->string('cnic')->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}


