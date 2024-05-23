<?php

namespace App\Constants;


class MallConstant
{
    // Mall 리스트
    public const MALL_EASYSELL  = "easySell";
    public const MALL_ONCHANNEL = "onchannel";

    public const MALL_LIST = [
        self::MALL_EASYSELL,
        self::MALL_ONCHANNEL,
    ];

    /** 상품등록상태 (등록 / 미등록) */
    public const UNREGIST = "S";
    public const REGISTED = "Y";
    
    public const REGIST_SUCCESS = "Y";
    public const REGIST_FAIL    = "N";
    public const REGIST_ERROR   = "E";

    public const MODI_SUCCESS = "Y";
    public const MODI_FAIL    = "N";
}
