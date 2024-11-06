<?php

namespace App\Http\Request\Bonaera;

use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraInUpdateRequest extends FormRequest
{
    public string $type;
    public string $stockNo;

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'type.required'     => BonaeraErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
            'stock_no.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("STOCK_NO"),
        ];
    }

    public function rules()
    {
        return [
            'type'     => ['required', 'string', new BonaeraInTypeRule],
            'stock_no' => 'required|string',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, 
            helpers_json_response(HttpConstant::BAD_REQUEST, [], $validator->errors()->first())
        );
    }

    /**
     * 유효성 검사 후 속성 설정
     */
    protected function passedValidation()
    {
        $this->type    = $this->input('type');
        $this->stockNo = $this->input('stock_no');
    }
}