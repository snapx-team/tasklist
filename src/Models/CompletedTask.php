<?php

namespace Xguard\Tasklist\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Completed Task Model
 *
 * @package Xguard\Tasklist\Models
 * @property int $id
 * @property int $task_id
 * @property int $job_site_shift_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Task $Task
 * @method static Builder|CompletedTask newModelQuery()
 * @method static Builder|CompletedTask newQuery()
 * @method static Builder|CompletedTask query()
 * @method static Builder|CompletedTask whereCreatedAt($value)
 * @method static Builder|CompletedTask whereId($value)
 * @method static Builder|CompletedTask whereJobSiteShiftId($value)
 * @method static Builder|CompletedTask whereTaskId($value)
 * @method static Builder|CompletedTask whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CompletedTask extends Model
{
    protected $table = 'tl_completed_tasks';
    protected $guarded = [];

    public function Task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
