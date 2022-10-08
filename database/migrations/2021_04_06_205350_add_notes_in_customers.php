<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesInCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasColumn('customer', 'notes'))
        {
            Schema::table('customer', function(Blueprint $table) {

                $table->text('notes')->nullable();

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
        Schema::table('customer', function(Blueprint $table) {

                $table->dropColumn('notes');

            });
    }
}
