<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'name'=> $this->contract_identifier,
            'jobSite' => new JobSiteResource($this->whenLoaded('jobSite'))
        ];
    }
}
