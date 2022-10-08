<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLengthWidthHeightInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'length')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('length', 16, 2)->nullable();
            });
        }
        if (!Schema::hasColumn('products', 'width')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('width', 16, 2)->nullable();
            });
        }
        if (!Schema::hasColumn('products', 'height')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('height', 16, 2)->nullable();
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
        if (Schema::hasColumn('products', 'length')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('length');
            });
        }
        if (Schema::hasColumn('products', 'width')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('width');
            });
        }
        if (Schema::hasColumn('products', 'height')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('height');
            });
        }
    }
}
