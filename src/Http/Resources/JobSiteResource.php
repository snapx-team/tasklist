<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JobSiteResource
 *
 * @package Xguard\Tasklist\Http\Resources
 *
 * This class represents a Laravel resource for job sites.
 */
class JobSiteResource extends JsonResource
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
            'id' => $this->id,
            'address' => $this->address,
            'region' => $this->region,
            'lat' => $this->google_coordinates_lat,
            'lng' => $this->google_coordinates_lng,
            'jobSiteImagePath' => $this->job_site_image_path,
            'onSiteClientContactName' => $this->on_site_client_info_contact_name,
            'onSiteClientContactPhone' => $this->on_site_client_info_phone_number,
            'onSiteClientPosition' => $this->on_site_client_info_position,
            'onSiteClientEmail' => $this->on_site_client_info_email,
            'clientNeeds' => $this->client_info_needs,
            'clientType' => $this->client_type,
            'jobSiteType' => $this->job_site_type,
            'workEnvironment' => $this->work_environment,
            'addedXguardServices' => $this->added_xguard_services,
            'accessToEstablishment' => $this->access_to_establishment,
            'typeOfClothing' => $this->type_of_clothing,
            'contracts' => ContractResource::collection($this->whenLoaded('contracts')),
        ];
    }
}
