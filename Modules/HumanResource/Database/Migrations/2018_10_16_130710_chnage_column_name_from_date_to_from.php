<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChnageColumnNameFromDateToFrom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
       Schema::table('holiday', function (Blueprint $table) {

            // $table->renameColumn('date', 'from');
            $table->dropColumn("date");


        });
       Schema::table('holiday', function (Blueprint $table) {

            // $table->renameColumn('date', 'from');
            $table->date('from');



        });
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
