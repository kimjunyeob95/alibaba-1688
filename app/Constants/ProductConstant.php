<?php

namespace App\Constants;


class ProductConstant
{
    // 제품채널
    public const CHANNE_PRICE_FREE = 1;
    public const CHANNE_DISTRIBUTION_SCIENCE = 2;
    public const CHANNE_PRICE_FOLLOW = 3;
    public const CHANNE_GROUP_PURCHASE = 4;
    public const CHANNE_CLOSE_MALL = 5;
    public const CHANNE_COMPLEX_MALL = 6;
    public const CHANNE_SPECIAL_PROMOTIONAL = 7;
    public const CHANNE_OVERSEAS_MARKET = 8;
    public const CHANNE_CLOSED_MALL_LIMITED = 9;
    public const CHANNE_SPECIALIZED_MALL = 10;
    public const CHANNE_BOX_UNIT = 11;
    public const CHANNE_OEM_UNIT = 12;
    public const CHANNE_FRANCHISE = 13;
    public const CHANNE_PRICE_STRIC_OBSERVANCE = 14;
    public const CHANNE_PRICE_FREE_2 = 15;
    public const CHANNE_PRICE_FOLLOW_2 = 16;
    public const CHANNE_EVENT_PRODUCT = 17;
    public const CHANNE_FOREGIN_DELIVERY = 21;
    public const CHANNE_FOREGIN_CHANNEL = 22;
    public const CHANNE_STOCK_CHANNEL = 24;
    public const CHANNE_PRICE_FOLLOW_B2B = 25;
    public const CHANNE_PRICE_SOCIAL_BUSINESS = 26;
    public const CHANNE_PRICE_SMALL_BUSINESS = 27;
    public const PRD_CHANNEL = [
        self::CHANNE_PRICE_FREE             => "가격자율",
        self::CHANNE_PRICE_FREE_2           => "가격자율",
        self::CHANNE_PRICE_FOLLOW           => "가격준수",
        self::CHANNE_PRICE_FOLLOW_2         => "가격준수",
        self::CHANNE_PRICE_FOLLOW_B2B       => "단독상품관",
        self::CHANNE_GROUP_PURCHASE         => "공동구매",
        self::CHANNE_CLOSE_MALL             => "폐쇄몰",
        self::CHANNE_COMPLEX_MALL           => "종합몰",
        self::CHANNE_FOREGIN_DELIVERY       => "해외직배송",
        self::CHANNE_FOREGIN_CHANNEL        => "해외채널",
        self::CHANNE_PRICE_STRIC_OBSERVANCE => "엄격준수",
        self::CHANNE_PRICE_SOCIAL_BUSINESS  => "틈새상품관",
        self::CHANNE_PRICE_SMALL_BUSINESS   => "소상공인",
        self::CHANNE_DISTRIBUTION_SCIENCE   => "유통과학센터",
        self::CHANNE_SPECIAL_PROMOTIONAL    => "특판/판촉물",
        self::CHANNE_OVERSEAS_MARKET        => "해외마켓",
        self::CHANNE_CLOSED_MALL_LIMITED    => "폐쇄몰(한정)",
        self::CHANNE_SPECIALIZED_MALL       => "전문몰",
        self::CHANNE_BOX_UNIT               => "박스단위",
        self::CHANNE_OEM_UNIT               => "OEM단위",
        self::CHANNE_FRANCHISE              => "프랜차이즈",
        self::CHANNE_EVENT_PRODUCT          => "이벤트상품",
        self::CHANNE_STOCK_CHANNEL          => "재고채널"
    ];

    // 과세여부
    public const TAX_TAXATION = 1; // 과세

    // 미성년판매 금지 여부
    public const MINOR_NOT_SALE_YES = "Y"; // 미성년판매 판매금지
    public const MINOR_NOT_SALE_NO  = "N"; // 미성년판매 판매

    // 공급업체 분류
    public const SUPP_SEC_1 = 1;
    public const SUPP_SEC_2 = 2;
    public const SUPP_SEC_3 = 3;
    public const SUPP_SEC_LIST = [
        self::SUPP_SEC_1 => "제조사",
        self::SUPP_SEC_2 => "벤더사",
        self::SUPP_SEC_3 => "수입사",
    ];

    // 옵션 상태
    public const OPTION_SEC_ON_SALE_NUMBER      = 1;
    public const OPTION_SEC_DISCONTINUED_NUMBER = 2;
    public const OPTION_SEC_OUT_OF_STOCK_NUMBER = 3;
    public const OPTION_SEC_ON_SALE             = "정상(재입고)";
    public const OPTION_SEC_DISCONTINUED        = "단종";
    public const OPTION_SEC_OUT_OF_STOCK        = "품절";
    public const OPTION_STATUS = [
        self::OPTION_SEC_ON_SALE_NUMBER      => self::OPTION_SEC_ON_SALE,
        self::OPTION_SEC_DISCONTINUED_NUMBER => self::OPTION_SEC_DISCONTINUED,
        self::OPTION_SEC_OUT_OF_STOCK_NUMBER => self::OPTION_SEC_OUT_OF_STOCK,
    ];

    public const MAPPING_CHANNEL = "onchannel";

    public const CHANNE_FOREGIN_CHANNEL_RETURN_COMMENT = "- 해외직배송 상품 특성상 교환/반품 시 왕복 해외배송비는 고객 부담이므로 반드시 확인해주세요.<br>
    - 본 상품은 해외직배송 상품으로 제품 출고 후 취소가 불가능합니다. 신중한 구매 부탁드립니다.<br>
    - 상품 수령일로부터 7일 이내에 먼저 판매자와 연락하셔서 반품 사유, 택배사, 배송비, 반송처 등을 협의하신 후 상품을 반품하시기 바랍니다.";
}
