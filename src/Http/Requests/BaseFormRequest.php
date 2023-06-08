<?php

namespace Xguard\Tasklist\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BaseFormRequest extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => 'false',
            'errors' => $validator->errors(),
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You\'re not authorized to do this request',
            'exception' => 'This action requires more permissions'
        ], ResponseAlias::HTTP_UNAUTHORIZED));
    }
}
