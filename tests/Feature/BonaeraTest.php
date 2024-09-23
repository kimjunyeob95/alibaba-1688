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
            "itemList" => [
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
        dd(json_encode($payload, JSON_UNESCAPED_UNICODE), $result);
    }

    # php artisan test --filter testCreateShipping
    /** 출고신청 */
    public function testCreateShipping()
    {
        $endPoint = "https://bonaera.com/elpisapi/application_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"  => BonaeraConstant::USER_ID,
            "ctrNum"  => 2,
            "RecInfo" => [
                [
                    "receiverName"  => "홍길동",
                    "zipCode"       => "123456",
                    "addr1"         => "서울특별시 강남역",
                    "addr2"         => "201호",
                    "receiverPhone" => "01025466499",
                    "personalNum"   => "test01",
                    "shipMemo"      => "문 앞에 놓아주세요."
                ]
            ],
            "itemList" => [
                [
                    "stockitemCode" => "IT240920000102",
                    "orderNumber"   => "2294987916953135493",
                    "productCount"  => 1,
                    "siteUrl"       => "https://trade.1688.com/order/offer_snapshot.htm?order_entry_id=2294987916954135493",
                    "localFee"      => 3.5
                ],
                [
                   "stockitemCode" => "IT240920000103",
                   "orderNumber"   => "2294987916953135493",
                   "productCount"  => 1,
                   "siteUrl"       => "https://trade.1688.com/order/offer_snapshot.htm?order_entry_id=2294987916955135493",
                   "localFee"      => 3.5
                ]
            ]
        ];
        $result = helpers_curl("POST", $endPoint, $header, $payload);
        dd(json_encode($payload, JSON_UNESCAPED_UNICODE), $result);
    }
}
