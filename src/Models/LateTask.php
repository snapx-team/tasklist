<?php

namespace Xguard\Tasklist\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Late Task Model
 *
 * @package Xguard\Tasklist\Models
 * @property int $id
 * @property int $task_id
 * @property int $notification_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task $Task
 * @method static Builder|LateTask newModelQuery()
 * @method static Builder|LateTask newQuery()
 * @method static Builder|LateTask query()
 * @method static Builder|LateTask whereCreatedAt($value)
 * @method static Builder|LateTask whereId($value)
 * @method static Builder|LateTask whereNotificationCount($value)
 * @method static Builder|LateTask whereTaskId($value)
 * @method static Builder|LateTask whereUpdatedAt($value)
 */

class LateTask extends Model
{
    protected $table = 'tl_late_tasks';
    protected $guarded = [];

    public function Task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
