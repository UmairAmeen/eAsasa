<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTbSettingsToAddDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('module',64)->after('id');
            $table->text('defaultValue')->nullable()->after('value');
            $table->integer('updated_by')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('module');
            $table->dropColumn('defaultValue');
            $table->dropColumn('updated_by');
        });
    }
}
