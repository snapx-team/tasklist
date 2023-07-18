<?php

namespace Xguard\Tasklist\Models;

use App\Models\Contract;
use App\Models\JobSiteShift;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Xguard\Tasklist\QueryBuilders\TaskQueryBuilder;

/**
 * Task Model
 *
 * @package Xguard\Tasklist\Models
 * @property int $id
 * @property int|null $is_recurring
 * @property string|null $description
 * @property string|null $time
 * @property int|null $contract_id
 * @property int|null $job_site_address_id
 * @property int|null $employee_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|JobSiteShift[] $completedTasks
 * @property-read int|null $completed_tasks_count
 * @property-read Contract|null $contract
 * @property-read Collection|TaskRecurrence[] $taskRecurrence
 * @property-read int|null $task_recurrence_count
 * @method static TaskQueryBuilder|Task getTasksByContractAndAddressForASpecificDateOrDayOfWeek(int $contractId, ?int $jobSiteAddressId, string $date, int $dayOfWeekId)
 * @method static TaskQueryBuilder|Task newModelQuery()
 * @method static TaskQueryBuilder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static TaskQueryBuilder|Task query()
 * @method static TaskQueryBuilder|Task whereContractId($value)
 * @method static TaskQueryBuilder|Task whereCreatedAt($value)
 * @method static TaskQueryBuilder|Task whereDeletedAt($value)
 * @method static TaskQueryBuilder|Task whereDescription($value)
 * @method static TaskQueryBuilder|Task whereEmployeeId($value)
 * @method static TaskQueryBuilder|Task whereId($value)
 * @method static TaskQueryBuilder|Task whereIsIncompleteAndPastDeadline()
 * @method static TaskQueryBuilder|Task whereIsRecurring($value)
 * @method static TaskQueryBuilder|Task whereJobSiteAddressId($value)
 * @method static TaskQueryBuilder|Task whereTime($value)
 * @method static TaskQueryBuilder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 * @mixin Eloquent
 */
class Task extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'tl_tasks';
    protected $guarded = [];

    const ID = 'id';
    const CONTRACT_ID = 'contract_id';
    const JOB_SITE_ADDRESS_ID = 'job_site_address_id';
    const IS_RECURRING = 'is_recurring';
    const TIME = 'time';
    const TASK_RECURRENCE_RELATION_NAME = 'taskRecurrence';
    const COMPLETED_TASKS_RELATION_NAME = 'completedTasks';

    public function newEloquentBuilder($query): TaskQueryBuilder
    {
        return new TaskQueryBuilder($query);
    }

    public function taskRecurrence(): HasMany
    {
        return $this->hasMany(TaskRecurrence::class, 'task_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function completedTasks(): BelongsToMany
    {
        return $this->belongsToMany(JobSiteShift::class, 'tl_completed_tasks')->withPivot('created_at');
    }

    public function completeTask(JobSiteShift $jobSiteShift)
    {
        $this->jobSiteShifts()->attach($jobSiteShift);
    }


}
