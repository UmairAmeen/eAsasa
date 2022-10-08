<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmsStatusInSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sms', 'status')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->string('status')->nullable();
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
        if (Schema::hasColumn('sms', 'status')) {
            Schema::table('sms', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
