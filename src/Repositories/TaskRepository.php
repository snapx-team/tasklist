<?php

namespace Xguard\Tasklist\Repositories;

use App\Helpers\DateTimeHelper;
use App\Models\JobSiteShift;
use App\Models\UserShift;
use Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Notification;
use stdClass;
use Throwable;
use Xguard\Tasklist\Http\Resources\TaskResource;
use Xguard\Tasklist\Models\LateTask;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;
use Xguard\Tasklist\Notifications\TaskSlackNotification;

/**
 * TaskRepository Class
 *
 * @package Xguard\Tasklist\Repositories
 */
class TaskRepository
{
    /**
     * Get all tasks for a specific contract
     *
     * @param int $id
     * @return AnonymousResourceCollection
     * @static
     */
    public static function getGlobalContractTasks(int $id): AnonymousResourceCollection
    {
        $tasks = Task::where(Task::CONTRACT_ID, $id)
            ->whereNull(Task::JOB_SITE_ADDRESS_ID)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();
        return TaskResource::collection($tasks);
    }

    /**
     * Get all tasks for a specific job site address
     *
     * @param int $id
     * @return AnonymousResourceCollection
     * @static
     */
    public static function getJobSiteAddressTasks(int $id): AnonymousResourceCollection
    {
        $tasks = Task::where(Task::JOB_SITE_ADDRESS_ID, $id)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();
        return TaskResource::collection($tasks);
    }

    /**
     * Create Task
     *
     * @param array $data
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function createTask(array $data): JsonResponse
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

            if ($task->is_recurring) {
                foreach ($data['selectedDaysOfWeek'] as $day) {
                    $taskRecurrence = new TaskRecurrence();
                    $taskRecurrence->day_of_week_id = $day['id'];
                    $taskRecurrence->task_id = $task->id;
                    $taskRecurrence->save();
                }
            }

            DB::commit();
            return response()->json(['success' => true,]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Edit Task
     *
     * @param int $id
     * @param string $description
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function editTask(int $id, string $description): JsonResponse
    {
        try {
            DB::beginTransaction();
            $task = Task::find($id);
            $task->description = $description;
            $task->save();
            DB::commit();
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Create a completed task
     *
     * @param int $jobSiteShiftId
     * @param int $taskId
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function createCompletedTask(int $jobSiteShiftId, int $taskId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $task = Task::find($taskId);
            $jobSiteShift = JobSiteShift::find($jobSiteShiftId);


            $task->completeTask($jobSiteShift);
            DB::commit();
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Returns a formatted collection of all tasks that are during the full days of a specific job site shift.
     *
     * @param int $jobSiteShiftId
     * @return AnonymousResourceCollection
     * @static
     */
    public static function getEmployeeDailyTasks(int $jobSiteShiftId): AnonymousResourceCollection
    {
        $jobSiteShift = JobSiteShift::find($jobSiteShiftId);
        $startDate = carbon::parse($jobSiteShift->shift_start);
        $endDate = carbon::parse($jobSiteShift->shift_end);
        $contractId = $jobSiteShift->contract_id;
        $jobSiteAddressId = $jobSiteShift->subaddress_id;
        $currentDate = clone $startDate;
        $daysOfWeekData = [];

        do {
            $daysOfWeekData[] = [
                'shiftDay' => $currentDate->toDateString(),
                'dayOfWeekId' => $currentDate->dayOfWeekIso,
            ];
            $currentDate->addDay();
        } while (($currentDate->dayOfWeekIso !== $endDate->dayOfWeekIso));
        $concatenatedTasks = collect();

        foreach ($daysOfWeekData as $dayOfWeekData) {

            $tasks = Task::getTasksByContractAndAddressForASpecificDateOrDayOfWeek($contractId, $jobSiteAddressId, $dayOfWeekData['shiftDay'], $dayOfWeekData['dayOfWeekId'])->get();
            $tasks = $tasks->map(function ($task) use ($dayOfWeekData) {
                $task['dayOfWeekId'] = $dayOfWeekData['dayOfWeekId'];
                $task['shiftDay'] = $dayOfWeekData['shiftDay'];
                return $task;
            });

            $concatenatedTasks = $concatenatedTasks->concat($tasks);
        }

        return TaskResource::collection($concatenatedTasks);
    }

    /**
     * Send a Slack notification whenever there is an incomplete task that is past the deadline.
     *
     * current behavior:
     * Notify if the task is more than 1 hour late and no notification has been sent before,
     * or if the task is more than 4 hours late and a notification has been sent only once before.
     *
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function notifyAllLateTasks(): JsonResponse
    {
        try {
            DB::beginTransaction();

            $tasks = Task::whereIsIncompleteAndPastDeadline()->get();
            $data = new stdClass();

            foreach ($tasks as $task) {

                $taskTime = Carbon::parse($task->time);

                if ($taskTime->toTimeString() < DateTimeHelper::now()->toTimeString()) {
                    $taskTimeConverted = DateTimeHelper::today()->setTime($taskTime->hour, $taskTime->minute);
                } else {
                    $taskTimeConverted = DateTimeHelper::today()->subDay()->setTime($taskTime->hour, $taskTime->minute);
                }

                $taskLog = LateTask::firstOrCreate(
                    ['task_id' => $task->id],
                    ['notification_count' => 0]
                );

                $notify = false;
                if (($taskTimeConverted->diffInHours(DateTimeHelper::now()) >= 1 && $taskLog->notification_count === 0)
                    || ($taskTimeConverted->diffInHours(DateTimeHelper::now()) >= 4 && $taskLog->notification_count === 1)) {
                    $taskLog->notification_count += 1;
                    $taskLog->save();
                    $notify = true;
                }

                if ($notify) {

                    $data->{$task->id} = new stdClass();

                    $jobSiteShifts = JobSiteShift::where('contract_id', $task->contract_id)
                        ->where('subaddress_id', $task->job_site_address_id)
                        ->where('shift_start', '<', $taskTimeConverted)
                        ->where('shift_end', '>', $taskTimeConverted)
                        ->get();

                    $taskEmployees = [];

                    foreach ($jobSiteShifts as $jobSiteShift) {

                        if ($jobSiteShift->id)
                            $userShifts = UserShift::where('job_site_shift_id', $jobSiteShift->id)->get();

                        foreach ($userShifts as $userShift) {
                            $employee = $userShift->employee;
                            $taskEmployees[] = [
                                'employeeName' => $employee->full_name,
                                'phone' => $employee->tel_1,
                                'id' => $employee->id,
                                'expo_push_token' => $employee->full_name,
                            ];
                        }
                    }
                    $data->{$task->id}->employees = $taskEmployees;
                    $data->{$task->id}->description = $task->description;
                    $data->{$task->id}->time = $taskTimeConverted->toDateTimeString();
                    $data->{$task->id}->contractIdentifier = $task->contract->contract_identifier;
                }
            }

            if (!empty((array)$data)) {

                Notification::route('slack', env('SLACK_WEBHOOK_LATE_TASK'))
                    ->notify(new TaskSlackNotification(json_decode(json_encode($data), true)));
            }

            DB::commit();
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
