<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Models\TaskRecurrence;
use Xguard\Tasklist\Repositories\TaskRepository;

class TaskController extends Controller
{
    public function getGlobalContractTasks($contractId)
    {
        return TaskRepository::getGlobalContractTasks($contractId);
    }

    public function getJobSiteTasks($jobSiteId)
    {
        return TaskRepository::getJobSiteTasks($jobSiteId);
    }

    public function getEmployeeDailyTasks($contractId, $jobSiteId)
    {
        return TaskRepository::getEmployeeDailyTasks($contractId, $jobSiteId);
    }

    public function createTask(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();

            $task = new Task();
            $task->description = $data['description'];
            $task->contract_id = $data['contractId'];
            $task->job_site_id = $data['jobSiteId'];
            $task->time = $data['time'];
            $task->is_recurring = $data['isRecurring'];
            $task->employee_id = session('employee_id');
            $task->save();

            foreach ($data['selectedDaysOfWeek'] as $day) {
                $taskRecurrence = new TaskRecurrence();
                $taskRecurrence->day_of_week_id = $day['id'];
                $taskRecurrence->task_id = $task->id;
                $taskRecurrence->save();
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function deleteTask($id)
    {
        try {
            Task::destroy($id);
        } catch (\Exception $e) {
            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
