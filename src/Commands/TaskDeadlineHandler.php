<?php

namespace Xguard\Tasklist\Commands;

use App\Helpers\DateTimeHelper;
use App\Models\JobSiteShift;
use App\Models\User;
use App\Models\UserShift;
use Carbon;
use Illuminate\Console\Command;
use Throwable;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;
use Xguard\Tasklist\Repositories\TaskRepository;

class TaskDeadlineHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasklist:task-deadline-handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the deadlines for the task to be completed within the last 4 hours. It will notify employees preemptively and also notify the employee and supervisors if it is late.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Throwable
     */
    public function handle()
    {
        return TaskRepository::notifyAllLateTasks();
    }
}
