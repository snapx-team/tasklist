<?php

namespace Xguard\Tasklist\Repositories;

use Carbon;
use Xguard\Tasklist\Http\Resources\TaskResource;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;

class TaskRepository
{
    public static function getGlobalContractTasks(int $id)
    {
        $tasks = Task::where(Task::CONTRACT_ID, $id)->with(Task::TASK_RECURRENCE_RELATION_NAME)->get();
        return TaskResource::collection($tasks);
    }

    public static function getJobSiteTasks(int $id)
    {
        $tasks = Task::where(Task::JOB_SITE_ID, $id)->with(Task::TASK_RECURRENCE_RELATION_NAME)->get();
        return TaskResource::collection($tasks);
    }

    public static function getEmployeeDailyTasks(int $contractId, int $jobSiteId)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentDayOfWeekId = Carbon::now()->dayOfWeekIso;

        $tasks = Task::where(Task::CONTRACT_ID, $contractId)
            ->where(function ($query) use ($jobSiteId) {
                $query->where(Task::JOB_SITE_ID, $jobSiteId)
                    ->orWhereNull(Task::JOB_SITE_ID);
            })
            ->where(function ($query) use ($currentDayOfWeekId, $currentDate) {
                $query->where(function ($query) use ($currentDate) {
                    $query->where(Task::IS_RECURRING, false)
                        ->whereDate(Task::TIME, $currentDate);
                    //I can change this to a whereDateBetween....
                })
                    ->orWhereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) use ($currentDayOfWeekId) {
                        $query->where(TaskRecurrence::DAY_OF_WEEK_ID, $currentDayOfWeekId);
                    });
            })
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();

        return TaskResource::collection($tasks);
    }
}
