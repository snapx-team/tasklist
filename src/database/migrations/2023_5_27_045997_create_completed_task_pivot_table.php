<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletedTaskPivotTable extends Migration
{
    public function up()
    {
        Schema::create('tl_completed_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('job_site_shift_id');
            $table->timestamps();
            $table->foreign('task_id')->references('id')->on('tl_tasks')->onDelete('cascade');
            $table->foreign('job_site_shift_id')->references('id')->on('job_site_shifts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tl_completed_tasks');
    }
}
