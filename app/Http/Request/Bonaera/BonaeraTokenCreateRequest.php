<?php

namespace App\Http\Request\Bonaera;

use App\Constants\HttpConstant;
use Illuminate\Foundation\Http\FormRequest; 

class BonaeraTokenCreateRequest extends FormRequest
{
    public string $userId;

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'user_id.required' => '아이디를 입력하세요.',
        ];
    }

    public function rules()
    {
        return [
            'user_id' => 'required|string',
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
        $this->userId = $this->input('user_id');
    }
}