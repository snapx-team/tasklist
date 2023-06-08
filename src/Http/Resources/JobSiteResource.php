<?php

namespace Xguard\Tasklist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobSiteResource extends JsonResource
{
    public function toArray($request)
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
