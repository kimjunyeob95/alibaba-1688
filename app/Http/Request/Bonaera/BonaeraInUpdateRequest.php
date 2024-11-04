<?php

namespace App\Http\Request\Bonaera;

use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraInUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'type.required'       => BonaeraErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
            'stock_code.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("STOCK_CODE"),
        ];
    }

    public function rules()
    {
        return [
            'type'       => ['required', 'string', new BonaeraTypeRule],
            'stock_code' => 'required|string',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, 
            helpers_json_response(HttpConstant::BAD_REQUEST, [], $validator->errors()->first())
        );
    }
}