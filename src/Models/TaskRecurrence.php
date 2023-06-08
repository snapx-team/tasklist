<?php

namespace Xguard\Tasklist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
