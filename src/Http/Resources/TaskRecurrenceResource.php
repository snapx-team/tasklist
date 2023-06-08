<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskRecurrenceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'dayOfWeekId'=> $this->day_of_week_id,
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
