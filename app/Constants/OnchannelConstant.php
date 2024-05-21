<?php

namespace App\Constants;


class OnchannelConstant
{
    public const ONCH1688     = "onch1688";
    public const NAT_SEC      = "KR";
    public const SUPP_SEC     = 3;
    public const TRANS_INFO   = "오후5시/온채널/7일 이상 소요";
    public const SEND_CHECK   = 1;
    public const TRANS_NM     = "대한통운";
    public const PRD_CHANNEL  = 30;
    public const SALE_NUM     = 3;
    public const ETC_COMMENT  = "해외배송 상품 입니다.";
    public const SEC_TAX      = "N";
    public const CATE_NUM     = 26;
    public const OP_RANK      = 1;
    public const DISC_PRICE   = 0;
    public const OPTION_PRICE = 0;
    public const VENDOR_PRICE = 0;
    public const TOTAL_COUNT  = 0;
    public const VOLUME       = "";
    public const AMOUNT       = 0;
    public const BRAND_INFO   = [
        "brand_nm"                 => "온채널X1688",
        "release_zipcode"          => "06158",
        "release_address"          => "서울특별시 강남구 테헤란로79길",
        "release_address_detail"   => "11-1",
        "release_phone"            => "01012345678",
        "release_mobile"           => "01012345678",
        "return_zipcode"           => "06158",
        "return_address"           => "서울특별시 강남구 테헤란로79길",
        "return_address_detail"    => "11-1",
        "return_phone"             => "01012345678",
        "return_mobile"            => "01012345678",
        "basic_delivery_charge"    => 3000,
        "jeju_delivery_charge"     => 5000,
        "extra_delivery_charge"    => 5000,
        "return_delivery_charge"   => 3000,
        "exchange_delivery_charge" => 3000,
        "delivery_id"              => 4
    ];
}
