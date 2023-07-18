<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * TaskRecurrenceResource
 *
 * @package Xguard\Tasklist\Http\Resources
 *
 * This class represents a Laravel resource for task recurrence.
 */
class TaskRecurrenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'=> $this->id,
            'dayOfWeekId'=> $this->day_of_week_id,
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
