<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ContractResource
 *
 * @package Xguard\Tasklist\Http\Resources
 *
 * This class represents a Laravel resource for contracts.
 */

class ContractResource extends JsonResource
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
            'name'=> $this->contract_identifier,
            'jobSite' => new JobSiteResource($this->whenLoaded('jobSite'))
        ];
    }
}
