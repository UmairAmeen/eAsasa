<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('admin_transaction')) {
            Schema::create('admin_transaction', function (Blueprint $table) {
                $table->increments('id');
                $table->date('date');
                $table->enum('type', array('in', 'out'));
                $table->string('bank', 50)->nullable();
                $table->string('transaction_id', 20)->nullable();
                $table->float('amount', 8,2);
                $table->date('release_date')->nullable();
                $table->enum('payment_type', array('cash', 'cheque', 'transfer'));
                $table->string('description')->nullable();
                $table->integer('added_by')->unsigned()->nullable();
                $table->integer('edited_by')->unsigned()->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        if (Schema::hasTable('admin_transaction')) {
            Schema::drop('admin_transaction');
        }
    }
}
