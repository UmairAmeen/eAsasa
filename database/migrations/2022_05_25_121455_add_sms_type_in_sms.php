<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmsTypeInSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sms', 'sms_type')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->string('sms_type')->nullable();
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
        if (Schema::hasColumn('sms', 'sms_type')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->dropColumn('sms_type');
            });
        }
    }
}
