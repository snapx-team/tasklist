<?php

namespace Xguard\Tasklist\Actions\Contracts;

use App\Models\Contract;
use Lorisleiva\Actions\Action;
use Xguard\Tasklist\Http\Resources\ContractResource;

class GetContractsDataAction extends Action
{
    const CONTRACTS_DATA = 'contractsData';

    public function handle(): array
    {
        $contracts = Contract::all();
        return [self::CONTRACTS_DATA => ContractResource::collection($contracts)];
    }
}
