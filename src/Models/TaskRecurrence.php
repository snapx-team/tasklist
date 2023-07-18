<?php

namespace Xguard\Tasklist\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Task Recurrence Model
 *
 * @package Xguard\Tasklist\Models
 * @property int $id
 * @property int $day_of_week_id
 * @property int $task_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task $Task
 * @method static Builder|TaskRecurrence newModelQuery()
 * @method static Builder|TaskRecurrence newQuery()
 * @method static Builder|TaskRecurrence query()
 * @method static Builder|TaskRecurrence whereCreatedAt($value)
 * @method static Builder|TaskRecurrence whereDayOfWeekId($value)
 * @method static Builder|TaskRecurrence whereId($value)
 * @method static Builder|TaskRecurrence whereTaskId($value)
 * @method static Builder|TaskRecurrence whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TaskRecurrence extends Model
{
    protected $table = 'tl_task_recurrence';
    protected $guarded = [];

    const DAY_OF_WEEK_ID = 'day_of_week_id';

    public function Task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
