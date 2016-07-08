<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function($table) {
            $table->integer('user_id')->nullable()->after('table');
            $table->string('action')->after('user_id');
            $table->text('old_value')->nullable()->after('action');
            $table->text('new_value')->nullable()->after('old_value');
            $table->dateTime('when')->after('new_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function($table) {
            $table->dropColumn('user_id');
            $table->dropColumn('action');
            $table->dropColumn('old_value');
            $table->dropColumn('new_value');
        });
    }
}
