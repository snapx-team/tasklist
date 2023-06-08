<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Collection;
use Xguard\Tasklist\Repositories\ErpContractsRepository;
use Xguard\Tasklist\Repositories\ErpUsersRepository;
use Xguard\Tasklist\Repositories\OnSiteEmployeeRepository;

/**
 * Class ErpController
 * @package Xguard\Tasklist\Http\Controllers
 * @group Employee Plugin
 */
class ErpController extends Controller
{

    public function getAllUsers(): Collection
    {
        return ErpUsersRepository::getAllUsers();
    }

    public function getSomeUsers($search): Collection
    {
        return ErpUsersRepository::getSomeUsers($search);
    }

    /**
     * Get Active Contracts
     * @responseFile public/vendor/tasklist/scribeResponses/getActiveContracts.json
     */
    public function getAllActiveContracts(): array
    {
        return ErpContractsRepository::getAllActiveContracts();
    }

    /**
     * Get Active Contracts With Search Term
     * @responseFile public/vendor/tasklist/scribeResponses/getActiveContracts.json
     */
    public function getSomeActiveContracts($search): array
    {
        return ErpContractsRepository::getSomeActiveContracts($search);
    }
}
