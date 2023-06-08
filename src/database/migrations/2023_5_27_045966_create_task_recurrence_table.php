<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRecurrenceTable extends Migration
{
    public function up()
    {
        Schema::create('tl_task_recurrence', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('day_of_week_id');
            $table->foreignId('task_id')->constrained('tl_tasks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tl_task_recurrence');
    }
}
