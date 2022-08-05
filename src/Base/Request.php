<?php

/**
 * @file request 请求基类
 */

namespace Ake\Tools\Base;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Request extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();
        throw new HttpResponseException(error($error));
    }

    public function authorize()
    {
        return true;
    }

}