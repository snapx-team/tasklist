<?php

namespace Xguard\Tasklist\QueryBuilders;

use App\Helpers\DateTimeHelper;
use Illuminate\Database\Eloquent\Builder;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;

/**
 * Class TaskQueryBuilder
 * @package Xguard\Tasklist\QueryBuilders
 */
class TaskQueryBuilder extends Builder
{
    /**
     * Get all the tasks that are incomplete within the last 4 hours
     *
     * @return TaskQueryBuilder
     * @static
     */
    public static function whereIsIncompleteAndWithinAnHourOrPastDeadline(): Builder
    {
        return Task::where(function ($query) {
            $query->where(function ($query) {
                $query->where(Task::IS_RECURRING, false)
                    ->where(Task::TIME, '>=', DateTimeHelper::now()->subHours(4))
                    ->where(Task::TIME, '<', DateTimeHelper::now()->addMinutes(30));
            })
                ->orWhere(function ($query) {
                    $query->where(Task::IS_RECURRING, true);
                    $query->where(function ($query) {
                        $now = DateTimeHelper::now();
                        $fourHoursAgo = DateTimeHelper::now()->subHours(4);
                        if ($now->dayOfWeek === $fourHoursAgo->dayOfWeek) {
                            $query->whereTime(Task::TIME, '>=', $fourHoursAgo)
                                ->whereTime(Task::TIME, '<', $now->addMinutes(30))
                                ->whereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) {
                                    $query->where(TaskRecurrence::DAY_OF_WEEK_ID, DateTimeHelper::now()->dayOfWeekIso);
                                });
                        } else {
                            $query->where(function ($query) use ($now) {
                                $query->whereTime(Task::TIME, '<', $now->addMinutes(30))
                                    ->whereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) {
                                        $query->where(TaskRecurrence::DAY_OF_WEEK_ID, DateTimeHelper::now()->dayOfWeekIso);
                                    });
                            })->orWhere(function ($query) use ($fourHoursAgo) {
                                $query->whereTime(Task::TIME, '>=', $fourHoursAgo)
                                    ->whereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) use ($fourHoursAgo) {
                                        $query->where(TaskRecurrence::DAY_OF_WEEK_ID, DateTimeHelper::now()->subDay()->dayOfWeekIso);
                                    });
                            });
                        }
                    });
                });

        })->whereDoesntHave(Task::COMPLETED_TASKS_RELATION_NAME, function ($query) {
            $query->where('tl_completed_tasks.created_at', '>=', DateTimeHelper::now()->subHours(8));
        });
    }

    /**
     * Gets all the tasks of a specific contract and job site sub-address for a specific date or day of week.
     * The condition is based on if the task is recurring or not.
     * Tasks are returned with completed tasks relation.
     *
     * @param int $contractId
     * @param int|null $jobSiteAddressId
     * @param int $dayOfWeekId day of week in iso format
     * @param string $date
     * @return TaskQueryBuilder
     * @static
     */
    public static function getTasksByContractAndAddressForASpecificDateOrDayOfWeek(int $contractId, ?int $jobSiteAddressId, string $date, int $dayOfWeekId): Builder
    {
        return Task::where(Task::CONTRACT_ID, $contractId)
            ->where(function ($query) use ($jobSiteAddressId) {
                $query->where(Task::JOB_SITE_ADDRESS_ID, $jobSiteAddressId)
                    ->orWhereNull(Task::JOB_SITE_ADDRESS_ID);
            })
            ->where(function ($query) use ($dayOfWeekId, $date) {
                $query->where(function ($query) use ($date) {
                    $query->where(Task::IS_RECURRING, false)
                        ->whereDate(Task::TIME, $date);
                })
                    ->orWhere(function ($query) use ($dayOfWeekId) {
                        $query->where(Task::IS_RECURRING, true)
                            ->whereHas(Task::TASK_RECURRENCE_RELATION_NAME, function ($query) use ($dayOfWeekId) {
                                $query->where(TaskRecurrence::DAY_OF_WEEK_ID, $dayOfWeekId);
                            });
                    });
            })
            ->with(Task::COMPLETED_TASKS_RELATION_NAME)
            ->orderByRaw("DATE_FORMAT(time, '%H:%i') ASC");
    }
}
