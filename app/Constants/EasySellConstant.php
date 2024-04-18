<?php

namespace App\Constants;


class EasySellConstant
{
    //상품 모드
    public const ITEM_REGIST = "I"; //등록
    public const ITEM_MODI   = "M"; //수정

    //셀러허브 아이디
    public const SELLERHUB_ID = "sellerhub";

    //api 기본정보
    public const LINKER_ID = "onchannel";
    public const USER_ID   = "2018637410_200"; //W_패션
    public const USER_PW   = "1234";

    // 상품 판매 상태
    public const STATUS_ON_SALE   = "001";
    public const STATUS_STOP_SALE = "004";

    public const ITEM_MADE_IN = "중국";

    public const API_SUCCESS = "SUCC";
    public const API_FAIL    = "FAIL";

    // 과세여부
    /** 과세 */
    public const TAX_TAXATION  = "001";
    /** 면세 */
    public const TAX_EXEMPTION = "002";

    //W 브랜드 코드
    public const W_ACCOUNT_FASHION     = 49096; //W_패션의류
    public const W_ACCOUNT_SPORTS      = 49097; //W_스포츠/레저
    public const W_ACCOUNT_GOODS       = 49098; //W_잡화/슈즈/쥬얼리
    public const W_ACCOUNT_BEAUTY      = 49099; //W_뷰티
    public const W_ACCOUNT_LUXURY      = 49100; //W_명품
    public const W_ACCOUNT_CHILD       = 49101; //W_출산/유아동
    public const W_ACCOUNT_LIFE        = 49102; //W_생활/건강
    public const W_ACCOUNT_PET         = 49103; //W_반려동물
    public const W_ACCOUNT_FOOD        = 49104; //W_식품
    public const W_ACCOUNT_DIGITAL     = 49105; //W_디지털/가전
    public const W_ACCOUNT_INTERIOR    = 49106; //W_가구/인테리어
    public const W_ACCOUNT_BOOK        = 49107; //W_도서/음반
    public const W_ACCOUNT_RADIO       = 49108; //W_무전기
    public const W_ACCOUNT_USED_LUXURY = 49109; //W_중고명품
    public const W_ACCOUNT_USED_GOODS  = 49110; //W_중고상품

    public const CATEGORY_MAPPING = [
        "066001" => self::W_ACCOUNT_FASHION,
        "066002" => self::W_ACCOUNT_SPORTS,
        "066003" => self::W_ACCOUNT_GOODS,
        "066004" => self::W_ACCOUNT_BEAUTY,
        "066005" => self::W_ACCOUNT_LUXURY,
        "066006" => self::W_ACCOUNT_CHILD,
        "066007" => self::W_ACCOUNT_LIFE,
        "066008" => self::W_ACCOUNT_PET,
        "066009" => self::W_ACCOUNT_FOOD,
        "066010" => self::W_ACCOUNT_DIGITAL,
        "066011" => self::W_ACCOUNT_INTERIOR,
        "066012" => self::W_ACCOUNT_BOOK,
        "066013" => self::W_ACCOUNT_RADIO,
        "066014" => self::W_ACCOUNT_USED_LUXURY,
        "066015" => self::W_ACCOUNT_USED_GOODS,
    ];

    public const CATEGORY_NAME = [
        "066001" => "W_패션의류",
        "066002" => "W_스포츠/레저",
        "066003" => "W_잡화/슈즈/쥬얼리",
        "066004" => "W_뷰티",
        "066005" => "W_명품",
        "066006" => "W_출산/유아동",
        "066007" => "W_생활/건강",
        "066008" => "W_반려동물",
        "066009" => "W_식품",
        "066010" => "W_디지털/가전",
        "066011" => "W_가구/인테리어",
        "066012" => "W_도서/음반",
        "066013" => "W_무전기",
        "066014" => "W_중고명품",
        "066015" => "W_중고상품",
    ];
}
