<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Repositories\TaskRepository;

/**
 * TaskController Class
 * @package Xguard\Tasklist\Http\Controllers
 */
class TaskController extends Controller
{
    /**
     * Get global contract tasks
     *
     * @param $contractId
     * @return AnonymousResourceCollection
     */
    public function getGlobalContractTasks($contractId) :AnonymousResourceCollection
    {
        return TaskRepository::getGlobalContractTasks($contractId);
    }

    /**
     * Get expired global contract tasks
     *
     * @param $contractId
     * @return AnonymousResourceCollection
     */
    public function getExpiredGlobalContractTasks($contractId) :AnonymousResourceCollection
    {
        return TaskRepository::getExpiredGlobalContractTasks($contractId);
    }


    /**
     * Get job site address tasks
     *
     * @param $jobSiteAddressId
     * @return AnonymousResourceCollection
     */
    public function getJobSiteAddressTasks($jobSiteAddressId): AnonymousResourceCollection
    {
        return TaskRepository::getJobSiteAddressTasks($jobSiteAddressId);
    }

    /**
     * Get expired job site address tasks
     *
     * @param $jobSiteAddressId
     * @return AnonymousResourceCollection
     */
    public function getExpiredJobSiteAddressTasks($jobSiteAddressId): AnonymousResourceCollection
    {
        return TaskRepository::getExpiredJobSiteAddressTasks($jobSiteAddressId);
    }

    /**
     * Get employee's tasks for the days associated to a job site shift
     *
     * @param int $jobSiteShiftId
     * @return mixed
     */
    public function getEmployeeDailyTasks(int $jobSiteShiftId)
    {
        $tasks = TaskRepository::getEmployeeDailyTasks($jobSiteShiftId);
        return $tasks->groupBy('shiftDay');
    }

    /**
     * Create Task
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function createTask(Request $request): JsonResponse
    {
        $data = $request->all();
        return TaskRepository::createTask($data);

    }

    /**
     * Create Completed Task
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function createCompletedTask(Request $request): JsonResponse
    {
        $jobSiteShiftId = $request->input('jobSiteShiftId');
        $taskId = $request->input('taskId');
        return TaskRepository::createCompletedTask($jobSiteShiftId, $taskId);

    }

    /**
     * Edit Task
     *
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function editTask($id, Request $request): JsonResponse
    {
        $newDescription = $request->input('description');
        return TaskRepository::editTask($id, $newDescription);
    }

    /**
     * Delete task
     *
     * @param $id
     * @return JsonResponse
     */
    public function deleteTask($id): JsonResponse
    {
        try {
            Task::destroy($id);
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
