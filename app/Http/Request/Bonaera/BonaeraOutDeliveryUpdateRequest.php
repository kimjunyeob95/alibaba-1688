<?php

namespace App\Http\Request\Bonaera;

use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraOutDeliveryUpdateRequest extends FormRequest
{
    public string $groupNo;
    public string $ctrNum;
    public string $receiverName;
    public string $personalNum;
    public string $receiverPhone;
    public string $personalType;
    public string $zipCode;
    public string $addr1;
    public string $shipMemo;
    
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'group_no.required'       => BonaeraErrorMessageConstant::getNotHaveErrorMessage("GROUP_NO"),
            'ctr_num.required'        => BonaeraErrorMessageConstant::getNotHaveErrorMessage("CTR_NUM"),
            'receiver_name.required'  => BonaeraErrorMessageConstant::getNotHaveErrorMessage("RECEIVER_NAME"),
            'personal_num.required'   => BonaeraErrorMessageConstant::getNotHaveErrorMessage("PERSONAL_NUM"),
            'receiver_phone.required' => BonaeraErrorMessageConstant::getNotHaveErrorMessage("RECEIVER_PHONE"),
            'personal_type.required'  => BonaeraErrorMessageConstant::getNotHaveErrorMessage("PERSONAL_TYPE"),
            'zip_code.required'       => BonaeraErrorMessageConstant::getNotHaveErrorMessage("ZIP_CODE"),
            'addr1.required'          => BonaeraErrorMessageConstant::getNotHaveErrorMessage("ADDR1")
        ];
    }

    public function rules()
    {
        return [
            'group_no'       => 'required|string',
            'ctr_num'        => 'required|string',
            'receiver_name'  => 'required|string',
            'personal_num'   => 'required|string',
            'receiver_phone' => 'required|string',
            'personal_type'  => 'required|string',
            'zip_code'       => 'required|string',
            'addr1'          => 'required|string'
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
        $this->groupNo       = $this->input('group_no');
        $this->ctrNum        = $this->input('ctr_num');
        $this->receiverName  = $this->input('receiver_name');
        $this->personalNum   = $this->input('personal_num');
        $this->receiverPhone = $this->input('receiver_phone');
        $this->personalType  = $this->input('personal_type');
        $this->zipCode       = $this->input('zip_code');
        $this->addr1         = $this->input('addr1');
        $this->shipMemo      = !empty($this->input('ship_memo')) ? $this->input('ship_memo') : "메모 없음";
    }
}