<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tl_tasks', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_recurring')->default(true);
            $table->text('description')->nullable();
            $table->dateTime('time')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('job_site_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tl_tasks');
    }
}
