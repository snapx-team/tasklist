<?php

namespace Xguard\Tasklist\Models;

use App\Models\User;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'tl_tasks';
    protected $guarded = [];

    const ID = 'id';
    const CONTRACT_ID = 'contract_id';
    const JOB_SITE_ID = 'job_site_id';
    const IS_RECURRING = 'is_recurring';
    const TIME = 'time';
    const TASK_RECURRENCE_RELATION_NAME = 'taskRecurrence';

    public function taskRecurrence(): HasMany
    {
        return $this->hasMany(TaskRecurrence::class, 'task_id');
    }
}
