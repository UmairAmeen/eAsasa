<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesInStockLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('stocklog', 'notes')) {
            Schema::table('stocklog', function (Blueprint $table) {
                $table->string('notes')->nullable();
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
        if (Schema::hasColumn('stocklog', 'notes')) {
            Schema::table('stocklog', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
}
