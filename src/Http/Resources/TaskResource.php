<?php

namespace Xguard\Tasklist\Http\Resources;

use App\Helpers\DateTimeHelper;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Xguard\Tasklist\Models\Task;

/**
 * TaskResource
 *
 * @package Xguard\Tasklist\Http\Resources
 *
 * This class represents a Laravel resource for Tasks.
 */
class TaskResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $time = $this->is_recurring ? Carbon::parse($this->time)->format('H:i') : $this->time;
        $completedTasks = $this->whenLoaded(TASK::COMPLETED_TASKS_RELATION_NAME);
        $isMissingValue = $completedTasks instanceof MissingValue;

        if (!$isMissingValue) {
            if ($this->is_recurring) {
                $lastTaskToBeCompletedBy = DateTimeHelper::parse($this->shiftDay . ' ' . $time);
            }
            else{
                $lastTaskToBeCompletedBy = DateTimeHelper::parse($this->time);
            }

            $isExpired = Carbon::now()->diffInHours($lastTaskToBeCompletedBy) >= 8;
            $isCompleted = $completedTasks->contains(function ($task) use ($lastTaskToBeCompletedBy, $time) {
                $taskCompletedAt = $task['pivot']['created_at'];
                return $taskCompletedAt->diffInHours($lastTaskToBeCompletedBy) < 8;
            });
        }

        $resourceArray = [
            'id' => $this->id,
            'description' => $this->description,
            'contractId' => $this->contract_id,
            'jobSiteAddressId' => $this->job_site_address_id,
            'time' => $time,
            'isRecurring' => $this->is_recurring,
            'employeeId' => $this->employee_id,
            'creator' => $this->whenLoaded('employee'),
            'jobSite' => new JobSiteResource($this->whenLoaded('jobSite')),
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'taskRecurrence' => TaskRecurrenceResource::collection($this->whenLoaded('taskRecurrence'))
        ];

        if (!$isMissingValue) {
            $resourceArray['isCompleted'] = $isCompleted;
            $resourceArray['isExpired'] = $isExpired;
        }

        return $resourceArray;
    }
}
