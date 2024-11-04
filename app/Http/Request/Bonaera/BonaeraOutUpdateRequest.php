<?php

namespace App\Http\Request\Bonaera;

use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraOutUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'type.required'     => BonaeraErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
            'group_no.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("GROUP_NO"),
            'sh_nos.required'   => BonaeraErrorMessageConstant::getNotHaveErrorMessage("SH_NOS"),
        ];
    }

    public function rules()
    {
        return [
            'type'     => ['required', 'string', new BonaeraOutTypeRule],
            'group_no' => 'required|string',
            'sh_nos'   => 'required|array',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, 
            helpers_json_response(HttpConstant::BAD_REQUEST, [], $validator->errors()->first())
        );
    }
}