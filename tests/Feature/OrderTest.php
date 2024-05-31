<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrderTest extends TestCase
{

    # 주문 생성
    # php artisan test --filter testW1OrderCreate
    public function testW1OrderCreate()
    {
        $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'flow'         => "general",
            'addressParam' => [
                'addressId'    => 4073018184,
                'fullName'     => 'SELLERHUB',
                'mobile'       => '15684570398',
                'phone'        => '15684570398',
                'postCode'     => '264206',
                'cityText'     => '山东省',
                'provinceText' => '威海市',
                'areaText'     => '环翠区',
                'townText'     => '温泉镇',
                'address'      => '柳林惠友路3号鸿泉服装院西',
                'districtCode' => '371002',
            ],
            'cargoParamList' => [
                [
                    "offerId" => 774890948567,
                    "specId" => '08bd0ae34e2a4d64b53f827a0dce8189',
                    "quantity" => 3,
                ],
                [
                    "offerId" => 774890948567,
                    "specId" => '1c53f6ea884a5b48a7b95e5221041ab6',
                    "quantity" => 2,
                ],
            ],
            'preSelectPayChannel' => "alipay"
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd($returnMsg);
    }

    # 결제
    # php artisan test --filter testW1OrderPay
    public function testW1OrderPay()
    {
        $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'flow'         => "general",
            'addressParam' => [
                'addressId'    => 4073018184,
                'fullName'     => 'SELLERHUB',
                'mobile'       => '15684570398',
                'phone'        => '15684570398',
                'postCode'     => '264206',
                'cityText'     => '山东省',
                'provinceText' => '威海市',
                'areaText'     => '环翠区',
                'townText'     => '温泉镇',
                'address'      => '柳林惠友路3号鸿泉服装院西',
                'districtCode' => '371002',
            ],
            'cargoParamList' => [
                [
                    "offerId" => 774890948567,
                    "specId" => '08bd0ae34e2a4d64b53f827a0dce8189',
                    "quantity" => 3,
                ],
                [
                    "offerId" => 774890948567,
                    "specId" => '1c53f6ea884a5b48a7b95e5221041ab6',
                    "quantity" => 2,
                ],
            ],
            'preSelectPayChannel' => "alipay"
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd($returnMsg);
    }
}
