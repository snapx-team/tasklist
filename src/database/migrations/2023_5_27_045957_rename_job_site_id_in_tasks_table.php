<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameJobSiteIdInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tl_tasks', function (Blueprint $table) {
            $table->renameColumn('job_site_id', 'job_site_address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tl_tasks', function (Blueprint $table) {
            $table->renameColumn('job_site_id', 'job_site_address_id');
        });
    }
}
