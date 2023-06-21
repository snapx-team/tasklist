<?php

namespace Xguard\Tasklist\Repositories;

use App\Helpers\DateTimeHelper;
use App\Models\JobSiteShift;
use Carbon;
use DB;
use Xguard\Tasklist\Http\Resources\TaskResource;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;

class TaskRepository
{
    public static function getGlobalContractTasks(int $id)
    {
        $tasks = Task::where(Task::CONTRACT_ID, $id)->whereNull(Task::JOB_SITE_ADDRESS_ID)->with(Task::TASK_RECURRENCE_RELATION_NAME)->get();
        return TaskResource::collection($tasks);
    }

    public static function getJobSiteTasks(int $id)
    {
        $tasks = Task::where(Task::JOB_SITE_ADDRESS_ID, $id)->with(Task::TASK_RECURRENCE_RELATION_NAME)->get();
        return TaskResource::collection($tasks);
    }

    public static function createTask(array $data)
    {
        try {
            DB::beginTransaction();

            $task = new Task();
            $task->description = $data['description'];
            $task->contract_id = $data['contractId'];
            $task->job_site_address_id = $data['jobSiteAddressId'];
            $task->time = $data['time'];
            $task->is_recurring = $data['isRecurring'];
            $task->employee_id = session('employee_id');
            $task->save();

            foreach ($data['selectedDaysOfWeek'] as $day) {
                $taskRecurrence = new TaskRecurrence();
                $taskRecurrence->day_of_week_id = $day['id'];
                $taskRecurrence->task_id = $task->id;
                $taskRecurrence->save();
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public static function editTask(int $id, string $description)
    {
        try {
            DB::beginTransaction();
            $task = Task::find($id);
            $task->description = $description;
            $task->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public static function getEmployeeDailyTasks(int $jobSiteShift)
    {
        $jobSiteShift = JobSiteShift::find($jobSiteShift);
        $startDate = carbon::parse($jobSiteShift->shift_start);
        $endDate = carbon::parse($jobSiteShift->shift_end);
        $startTime = $startDate->format('H:i:s');;
        $endTime = $endDate->format('H:i:s');
        $contractId = $jobSiteShift->contract_id;
        $jobSiteAddressId = $jobSiteShift->subaddress_id;
        $currentDate = clone $startDate;
        $daysOfWeekIds = [];

        while ($currentDate->lte($endDate)) {
            $daysOfWeekIds[] = $currentDate->dayOfWeekIso;
            $currentDate->addDay();
        }

        $tasks = Task::where(Task::CONTRACT_ID, $contractId)
            ->where(function ($query) use ($jobSiteAddressId) {
                $query->where(Task::JOB_SITE_ADDRESS_ID, $jobSiteAddressId)
                    ->orWhereNull(Task::JOB_SITE_ADDRESS_ID);
            })
            ->where(function ($query) use ($daysOfWeekIds, $startTime, $endTime, $endDate, $startDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->where(Task::IS_RECURRING, false)
                        ->whereBetween(Task::TIME, [$startDate, $endDate]);

                })
                    ->orWhereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) use ($endTime, $startTime, $daysOfWeekIds) {
                        $query->whereIn(TaskRecurrence::DAY_OF_WEEK_ID, $daysOfWeekIds)
                            ->whereTime(Task::TIME, '>=', $startTime)
                            ->whereTime(Task::TIME, '<=', $endTime);
                    });
            })
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();

        return TaskResource::collection($tasks);
    }
}
