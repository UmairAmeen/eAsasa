<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixInvoiceColume extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasColumn('invoice', 'discount'))
        {
            Schema::table('invoice', function(Blueprint $table) {

                $table->decimal('discount',18,2)->unsigned()->nullable();

            });
        }
        if (!Schema::hasColumn('invoice', 'total'))
        {
            Schema::table('invoice', function(Blueprint $table) {

                $table->decimal('total',18,2)->unsigned()->nullable();

            });
        }
        if (!Schema::hasColumn('invoice', 'date'))
        {
            Schema::table('invoice', function(Blueprint $table) {

                $table->date('date');

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
