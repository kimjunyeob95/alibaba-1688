<?php

namespace Tests\Feature;

use App\Constants\BonaeraConstant;
use Tests\TestCase;

class BonaeraTest extends TestCase
{

    # php artisan test --filter testCreateWarehouse
    /** 입고신청 */
    public function testCreateWarehouse()
    {
        $endPoint = "https://bonaera.com/elpisapi/stock_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"    => BonaeraConstant::USER_ID,
            "orderMemo" => "요청사항 test",
            "imtemList" => [
                [
                    "productShno"    => "444",
                    "productNameEng" => "아이스 실크 여성의 반팔 셔츠 여름 새로운 짧은 배 슬림",
                    "trackingNumber" => "434138687156253",
                    "productMoney"   => "48.92",
                    "productCount"   => "1",
                    "imgUrl"         => "https://cbu01.alicdn.com/img/ibank/O1CN01o8437h1aSjR5hMFy4_!!2216622853329-0-cib.jpg",
                    "option1"        => "❤❣❣❤콩 녹색❤❣❣_❤❣❣❤2XL (추천 65kg-72.5kg )❤❣❣",
                    "option2"        => "5368408",
                ],
                [
                    "productShno"    => "444",
                    "productNameEng" => "아이스 실크 여성의 반팔 셔츠 여름 새로운 짧은 배 슬림",
                    "trackingNumber" => "434138687156253",
                    "productMoney"   => "48.92",
                    "productCount"   => "1",
                    "imgUrl"         => "https://cbu01.alicdn.com/img/ibank/O1CN01o8437h1aSjR5hMFy4_!!2216622853329-0-cib.jpg",
                    "option1"        => "❤❣❣❤거위 핑크❤❣❣_❤❣❣❤2XL (추천 65kg-72.5kg )❤❣❣",
                    "option2"        => "5368413",
                ]
            ]
        ];
        $result = helpers_curl("POST", $endPoint, $header, $payload);
        dd($result);
        
    }
}
