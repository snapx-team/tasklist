<?php

namespace Xguard\Tasklist\Repositories;

use App\Actions\Users\SendPushNotificationToUserAction;
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
        $yesterday = Carbon::yesterday()->toDateString();

        $tasks = Task::where(Task::CONTRACT_ID, $id)
            ->where(function ($query) use ($yesterday) {
                $query->where(Task::IS_RECURRING, false)
                    ->whereDate(Task::TIME, '>=', $yesterday);
            })
            ->orWhere(Task::IS_RECURRING, true)
            ->whereNull(Task::JOB_SITE_ADDRESS_ID)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Get all expired tasks for a specific contract
     *
     * @param int $id
     * @return AnonymousResourceCollection
     * @static
     */
    public static function getExpiredGlobalContractTasks(int $id): AnonymousResourceCollection
    {
        $yesterday = Carbon::yesterday();

        $tasks = Task::where(Task::CONTRACT_ID, $id)
            ->where(Task::IS_RECURRING, false)
            ->whereDate(Task::TIME, '<', $yesterday)
            ->whereNull(Task::JOB_SITE_ADDRESS_ID)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderBy(Task::TIME, 'DESC')
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
        $yesterday = Carbon::yesterday();

        $tasks = Task::where(Task::JOB_SITE_ADDRESS_ID, $id)
            ->where(function ($query) use ($yesterday) {
                $query->where(Task::IS_RECURRING, false)
                    ->whereDate(Task::TIME, '>=', $yesterday);
            })
            ->orWhere(Task::IS_RECURRING, true)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC")
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * Get all expired tasks for a specific contract
     *
     * @param int $id
     * @return AnonymousResourceCollection
     * @static
     */
    public static function getExpiredJobSiteAddressTasks(int $id): AnonymousResourceCollection
    {
        $yesterday = Carbon::yesterday();

        $tasks = Task::where(Task::JOB_SITE_ADDRESS_ID, $id)
            ->where(Task::IS_RECURRING, false)
            ->whereDate(Task::TIME, '<', $yesterday)
            ->with(Task::TASK_RECURRENCE_RELATION_NAME)
            ->orderBy(Task::TIME, 'DESC')
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
            if ($currentDate->dayOfWeekIso === $endDate->dayOfWeekIso) {
                break;
            }
        } while ($currentDate->addDay());

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
     * Send a push notification to user within 15-30 minutes before task needs to be completed
     * Send a Slack notification whenever there is an incomplete task that is past the deadline.
     *
     * current behavior:
     * Send a push notification once to each employee on shift who is checked in up to 30 minutes before a task needs attention
     * Send push and Slack notification once if the task is more than 1 hour late
     * Send push and Slack notification once if the task is more than 3 hour late
     *
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function taskReminderNotifications(): JsonResponse
    {
        try {
            DB::beginTransaction();

            $tasks = Task::whereIsIncompleteAndWithinAnHourOrPastDeadline()->get();
            $data = new stdClass();

            foreach ($tasks as $task) {

                $taskTime = Carbon::parse($task->time);

                // only time portion is necessary for recurring task -> we convert the current day with the time of the recurring task.
                if ($taskTime->toTimeString() < DateTimeHelper::now()->addMinutes(30)->max(Carbon::parse('23:59'))->toTimeString()) {
                    $taskTimeConverted = DateTimeHelper::today()->setTime($taskTime->hour, $taskTime->minute);
                } else {
                    $taskTimeConverted = DateTimeHelper::today()->subDay()->setTime($taskTime->hour, $taskTime->minute);
                }

                $notifyLateTask = false;
                $sendEarlyReminderPushNotification = false;

                if ($taskTimeConverted > DateTimeHelper::now() && $taskTimeConverted->diffInMinutes(DateTimeHelper::now()) <= 15) {
                    $sendEarlyReminderPushNotification = true;
                } else {
                    $taskLog = LateTask::firstOrCreate(
                        ['task_id' => $task->id, 'time' => $taskTimeConverted],
                        ['notification_count' => 0]
                    );

                    if (($taskTimeConverted->diffInHours(DateTimeHelper::now()) >= 1 && $taskLog->notification_count === 0)
                        || ($taskTimeConverted->diffInHours(DateTimeHelper::now()) >= 3 && $taskLog->notification_count === 1)) {
                        $taskLog->notification_count += 1;
                        $taskLog->save();
                        $notifyLateTask = true;
                    }
                }

                if ($notifyLateTask || $sendEarlyReminderPushNotification) {

                    $data->{$task->id} = new stdClass();

                    $jobSiteShifts = JobSiteShift::where('contract_id', $task->contract_id)
                        ->when($task->job_site_address_id !== null, function ($query) use ($task) {
                            return $query->where('subaddress_id', $task->job_site_address_id);
                        })
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
                            ];

                            if ($sendEarlyReminderPushNotification) {
                                SendPushNotificationToUserAction::dispatch([
                                    'user' => $employee,
                                    'title' => $employee->locale === 'en' ? 'You have an upcoming task' : 'Vous avez une tâche bientôt',
                                    'body' => $task->description,
                                    'type' => 'TASK_NOTIFICATION',
                                ]);
                            } else {
                                SendPushNotificationToUserAction::dispatch([
                                    'user' => $employee,
                                    'title' => $employee->locale === 'en' ? 'There is an unfinished task that needs your attention' : 'Il y a une tâche incomplète qui requiert votre attention',
                                    'body' => $task->description,
                                    'type' => 'TASK_NOTIFICATION',
                                ]);
                            }
                        }
                    }
                    $data->{$task->id}->employees = $taskEmployees;
                    $data->{$task->id}->description = $task->description;
                    $data->{$task->id}->time = $taskTimeConverted->toDateTimeString();
                    $data->{$task->id}->contractIdentifier = $task->contract->contract_identifier;
                }
            }

            if (!empty((array)$data) && $notifyLateTask) {
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
