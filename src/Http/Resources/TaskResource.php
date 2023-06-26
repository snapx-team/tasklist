<?php

namespace Xguard\Tasklist\Http\Resources;

use Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Xguard\Tasklist\Models\Task;

class TaskResource extends JsonResource
{

    public function toArray($request)
    {

        $completedTasks = $this->whenLoaded(TASK::JOB_SITE_SHIFTS_RELATION_NAME);
        $isMissingValue = $completedTasks instanceof MissingValue;
        $isCompleted = false;
        if (!$isMissingValue) {
            $isCompleted = $completedTasks->contains(function ($task) {
                if (!$this->is_recurring) {
                    return true;
                }
                $taskCompletedAt = $task['pivot']['created_at'];
                $taskCompletedAt->setTimezone('America/Montreal');
                $taskCompletedAt = $taskCompletedAt->toDateTimeString();
                $shiftStart = carbon::parse($task['shift_start'])->subHours(4)->toDateTimeString();
                $shiftEnd = carbon::parse($task['shift_end'])->addHours(4)->toDateTimeString();
                return $taskCompletedAt >= $shiftStart && $taskCompletedAt <= $shiftEnd;
            });
        }

        return [
            'id' => $this->id,
            'description' => $this->description,
            'contractId' => $this->contract_id,
            'jobSiteAddressId' => $this->job_site_address_id,
            'time' => $this->is_recurring ? Carbon::parse($this->time)->format('H:i') : $this->time,
            'isRecurring' => $this->is_recurring,
            'employeeId' => $this->employee_id,
            'isCompleted' => $isCompleted,
            'creator' => $this->whenLoaded('employee'),
            'jobSite' => new JobSiteResource($this->whenLoaded('jobSite')),
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'taskRecurrence' => TaskRecurrenceResource::collection($this->whenLoaded('taskRecurrence')),
        ];
    }
}
