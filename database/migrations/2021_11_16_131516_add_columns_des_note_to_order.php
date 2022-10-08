<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDesNoteToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('order', 'note')) {
            Schema::table('order', function (Blueprint $table) {
                $table->string('note')->after('quantity')->nullable();
            });
        }
        if (!Schema::hasColumn('products', 'description')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('description')->nullable();
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
        if (Schema::hasColumn('order', 'note')) {
            Schema::table('order', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        
        if (Schema::hasColumn('products', 'description')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }


    }
}
