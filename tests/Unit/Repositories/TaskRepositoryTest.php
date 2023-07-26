<?php

namespace Tests\Unit\Repositories;

use App\Models\Contract;
use App\Models\JobSite;
use App\Models\JobSiteShift;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Tests\TestCase;
use Xguard\Tasklist\Http\Resources\TaskResource;
use Xguard\Tasklist\Models\Task;
use Xguard\Tasklist\Repositories\ErpContractsRepository;
use Xguard\Tasklist\Repositories\TaskRepository;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateTask()
    {
        // Arrange
        $data = [
            'description' => 'Sample Task',
            'contractId' => 1,
            'jobSiteAddressId' => null,
            'time' => '2023-07-25 15:00:00',
            'isRecurring' => true,
            'selectedDaysOfWeek' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        // Act
        TaskRepository::createTask($data);

        // Assert
        $this->assertDatabaseHas('tl_tasks', ['description' => 'Sample Task']);

    }

    public function testEditTask()
    {
        // Arrange
        $task = factory(Task::class)->create(['description' => 'Original Description']);
        $this->assertDatabaseHas('tl_tasks', ['id' => $task->id, 'description' => 'Original Description']);
        $newDescription = 'Updated Description';

        // Act
        TaskRepository::editTask($task->id, $newDescription);

        // Assert
        $this->assertDatabaseHas('tl_tasks', ['id' => $task->id, 'description' => 'Updated Description']);
    }

    public function testCreateCompletedTask()
    {
        // Arrange
        $task = factory(Task::class)->create();
        $jobSiteShift = factory(JobSiteShift::class)->create();

        // Act
        TaskRepository::createCompletedTask($task->id, $jobSiteShift->id);

        // Assert
        $this->assertDatabaseHas('tl_completed_tasks', ['task_id' => $task->id, 'job_site_shift_id' => $jobSiteShift->id]);
    }
}
