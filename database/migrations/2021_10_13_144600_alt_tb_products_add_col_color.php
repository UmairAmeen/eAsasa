<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AltTbProductsAddColColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'color')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('color', 255)->after('name')->nullable();
            });
        }
        if (!Schema::hasColumn('products', 'pattern')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('pattern', 255)->after('color')->nullable();
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
        if (Schema::hasColumn('products', 'color') && Schema::hasColumn('products', 'pattern')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('color');
                $table->dropColumn('pattern');
            });
        }
    }
}
