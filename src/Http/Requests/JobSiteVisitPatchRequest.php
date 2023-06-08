<?php

namespace Xguard\Tasklist\Http\Requests;

use Xguard\Tasklist\Models\JobSiteVisit;

class JobSiteVisitPatchRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return  [
            JobSiteVisit::ID => 'required|exists:Xguard\Tasklist\Models\JobSiteVisit,id',
        ];
    }
}
