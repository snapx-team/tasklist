<?php

namespace Xguard\Tasklist\Repositories;

use App\Models\Contract;
use Xguard\LaravelKanban\Entities\ErpContract;

class ErpContractsRepository
{
    public static function retrieve(int $erpContractId): ?ErpContract
    {
        $erpContract = Contract::find($erpContractId);
        return $erpContract ? new ErpContract($erpContract->id, $erpContract->contract_identifier) : null;
    }

    public static function getAllActiveContracts(): array
    {
        $erpContracts = Contract::with('jobSite.subaddresses')
            ->where('status', '=', 'active')
            ->orderBy('contract_identifier')->get();
        return self::formatContracts($erpContracts);
    }

    public static function getSomeActiveContracts($search): array
    {
        $erpContracts = Contract::with('jobSite')
            ->where('status', '=', 'active')
            ->where(function ($q) use ($search) {
                $q->where('contract_identifier', 'like', "%{$search}%");
            })->orderBy('contract_identifier')->take(10)->get();

        return self::formatContracts($erpContracts);
    }

    private static function formatContracts($erpContracts)
    {
        return $erpContracts->map(function ($erpContract) {

            $erpContract->addresses = collect([]);
            $erpContract->addresses->push([
                'id' => $erpContract->jobSite->id,
                'address' => $erpContract->jobSite->google_formatted_address,
                'lat' => $erpContract->jobSite->google_coordinates_lat,
                'lng' => $erpContract->jobSite->google_coordinates_lng,
                'isPrimaryAddress' => true
            ]);
            foreach ($erpContract['jobSite']['subaddresses'] as $subaddress) {
                $erpContract->addresses->push([
                    'id' => $subaddress->id,
                    'address' => $subaddress->formatted_address,
                    'lat' => $subaddress->latitude,
                    'lng' => $subaddress->longitude,
                    'isPrimaryAddress' => false
                ]);
            }
            $requiredPermits = [];

            if ($erpContract->jobSite->bsp_required) {
                array_push($requiredPermits, 'bsp');
            }
            if ($erpContract->jobSite->asp_required) {
                array_push($requiredPermits, 'asp');
            }
            if ($erpContract->jobSite->aqtr_required) {
                array_push($requiredPermits, 'aqtr');
            }
            if ($erpContract->jobSite->rcr_required) {
                array_push($requiredPermits, 'rcr');
            }
            if ($erpContract->jobSite->erailsafe_required) {
                array_push($requiredPermits, 'erailsafe');
            }

            return [
                'id' => $erpContract->id,
                'name' => $erpContract->contract_identifier,
                'jobSiteImagePath' => $erpContract->job_site_image_path,
                'onSiteClientContactName' => $erpContract->jobSite->on_site_client_info_contact_name,
                'onSiteClientContactPhone' => $erpContract->jobSite->on_site_client_info_phone_number,
                'onSiteClientPosition' => $erpContract->jobSite->on_site_client_info_position,
                'onSiteClientEmail' => $erpContract->jobSite->on_site_client_info_email,
                'clientNeeds' => $erpContract->jobSite->client_info_needs,
                'clientType' => $erpContract->jobSite->client_type,
                'jobSiteType' => $erpContract->jobSite->job_site_type,
                'workEnvironment' => $erpContract->jobSite->work_environment,
                'addedXguardServices' => $erpContract->jobSite->added_xguard_services,
                'accessToEstablishment' => $erpContract->jobSite->access_to_establishment,
                'typeOfClothing' => $erpContract->jobSite->type_of_clothing,
                'requiredPermits' => $requiredPermits,
                'protocols' => $erpContract->jobSite->protocols,
                'addresses' => $erpContract->addresses->toArray()
            ];
        })->all();
    }
}
