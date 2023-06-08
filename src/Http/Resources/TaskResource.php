<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'description'=> $this->description,
            'contractId'=> $this->contract_id,
            'jobSiteId'=> $this->job_site_id,
            'time'=> $this->time,
            'isRecurring'=> $this->is_recurring,
            'employeeId'=> $this->employee_id,
            'type'=> $this->job_site_id? 'contract': 'jobSite',
            'creator' => $this->whenLoaded('employee'),
            'jobSite' => new JobSiteResource($this->whenLoaded('jobSite')),
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'taskRecurrence' => TaskRecurrenceResource::collection($this->whenLoaded('taskRecurrence')),
        ];
    }
}
