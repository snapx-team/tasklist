<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeColumnToLateTaskTable extends Migration
{
    public function up()
    {
        Schema::table('tl_late_tasks', function (Blueprint $table) {
            $table->datetime('time')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tl_late_tasks', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }
}
