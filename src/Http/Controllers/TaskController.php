<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobSiteShift;
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

    public function getJobSiteTasks($jobSiteAddressId)
    {
        return TaskRepository::getJobSiteTasks($jobSiteAddressId);
    }

    public function getEmployeeDailyTasks(int $jobSiteShiftId)
    {
        return TaskRepository::getEmployeeDailyTasks($jobSiteShiftId);
    }

    public function createTask(Request $request)
    {
        $data = $request->all();
        return TaskRepository::createTask($data);

    }

    public function editTask($id, Request $request)
    {
        $newDescription = $request->input('description');
        return TaskRepository::editTask($id, $newDescription);
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
