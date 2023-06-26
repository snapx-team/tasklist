<?php

namespace Xguard\Tasklist\Models;

use App\Models\JobSiteShift;
use App\Models\User;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    const JOB_SITE_ADDRESS_ID = 'job_site_address_id';
    const IS_RECURRING = 'is_recurring';
    const TIME = 'time';
    const TASK_RECURRENCE_RELATION_NAME = 'taskRecurrence';
    const JOB_SITE_SHIFTS_RELATION_NAME = 'jobSiteShifts';

    public function taskRecurrence(): HasMany
    {
        return $this->hasMany(TaskRecurrence::class, 'task_id');
    }

    public function jobSiteShifts(): BelongsToMany
    {
        return $this->belongsToMany(JobSiteShift::class, 'tl_completed_tasks')->withPivot('created_at');
    }

    public function completeTask(JobSiteShift $jobSiteShift)
    {
        $this->jobSiteShifts()->attach($jobSiteShift);
    }
}
