<?php

namespace App\Http\Request\Bonaera;

use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraDeliveryBundleRequest extends FormRequest
{
    public string $type;
    public string $shNo;
    public string $originGroupNo;
    public string $changeGroupNo;

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'type.required'            => BonaeraErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
            'sh_no.required'           => BonaeraErrorMessageConstant::getNotHaveErrorMessage("SH_NO"),
            'origin_group_no.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("ORIGIN_GROUP_NO"),
            'change_group_no.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("CHANGE_GROUP_NO"),
        ];
    }

    public function rules()
    {
        return [
            'type'            => ['required', 'string', new BonaeraDeliveryBundleTypeRule],
            'sh_no'           => 'required|string',
            'origin_group_no' => 'required|string',
            'change_group_no' => 'required|string',
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
        $this->type          = $this->input('type');
        $this->shNo          = $this->input('sh_no');
        $this->originGroupNo = $this->input('origin_group_no');
        $this->changeGroupNo = $this->input('change_group_no');
    }
}