<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $table_names = ["customer", "invoice","transaction","products","stocklog"];
        foreach ($table_names as $key => $value) {
            # code...
            Schema::table($value, function (Blueprint $table) {
                $table->integer('added_by')->unsigned()->nullable();
                $table->integer('edited_by')->unsigned()->nullable();
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
        //
    }
}
