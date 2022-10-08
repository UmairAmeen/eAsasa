<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierIdInInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
         if (!Schema::hasColumn('invoice', 'supplier_id'))
        {
            Schema::table('invoice', function(Blueprint $table) {

                $table->integer('supplier_id')->unsigned()->nullable();

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
