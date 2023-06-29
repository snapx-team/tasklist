<?php

namespace Xguard\Tasklist\Repositories;

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
        $tasks = Task::where(Task::CONTRACT_ID, $id)
            ->whereNull(Task::JOB_SITE_ADDRESS_ID)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();
        return TaskResource::collection($tasks);
    }

    public static function getJobSiteTasks(int $id)
    {
        $tasks = Task::where(Task::JOB_SITE_ADDRESS_ID, $id)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();
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

            if($task->is_recurring ){
                foreach ($data['selectedDaysOfWeek'] as $day) {
                    $taskRecurrence = new TaskRecurrence();
                    $taskRecurrence->day_of_week_id = $day['id'];
                    $taskRecurrence->task_id = $task->id;
                    $taskRecurrence->save();
                }
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
        $contractId = $jobSiteShift->contract_id;
        $jobSiteAddressId = $jobSiteShift->subaddress_id;
        $currentDate = clone $startDate;
        $daysOfWeekData = [];

        while ($currentDate->lte($endDate)) {
            $daysOfWeekData[] = [
                'beginningOfDay' => clone $currentDate->startOfDay(),
                'shiftDay' => $currentDate->toDateString(),
                'dayOfWeekId' => $currentDate->dayOfWeekIso,
            ];
            $currentDate->addDay();
        }

        $concatenatedTasks = collect();

        foreach ($daysOfWeekData as $dayOfWeekData) {
            $tasks = Task::where(Task::CONTRACT_ID, $contractId)
                ->where(function ($query) use ($jobSiteAddressId) {
                    $query->where(Task::JOB_SITE_ADDRESS_ID, $jobSiteAddressId)
                        ->orWhereNull(Task::JOB_SITE_ADDRESS_ID);
                })
                ->where(function ($query) use ($dayOfWeekData) {
                    $query->where(function ($query) use ($dayOfWeekData) {
                        $query->where(Task::IS_RECURRING, false)
                            ->whereDate(Task::TIME, $dayOfWeekData['beginningOfDay']);
                    })
                        ->orWhere(function ($query) use ($dayOfWeekData) {
                            $query->where(Task::IS_RECURRING, true)
                                ->whereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) use ($dayOfWeekData) {
                                    $query->where(TaskRecurrence::DAY_OF_WEEK_ID, $dayOfWeekData['dayOfWeekId']);
                                });
                        });
                })
                ->with(Task::JOB_SITE_SHIFTS_RELATION_NAME)
                ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
                ->get();

            $tasks = $tasks->map(function ($task) use ($dayOfWeekData) {
                $task['dayOfWeekId'] = $dayOfWeekData['dayOfWeekId'];
                $task['shiftDay'] = $dayOfWeekData['shiftDay'];
                return $task;
            });

            $concatenatedTasks = $concatenatedTasks->concat($tasks);
        }

        return TaskResource::collection($concatenatedTasks);
    }

    public static function createCompletedTask(int $jobSiteShiftId, int $taskId)
    {
        try {
            DB::beginTransaction();

            $task = Task::find($taskId);
            $jobSiteShift = JobSiteShift::find($jobSiteShiftId);


            $task->completeTask($jobSiteShift);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
