<?php

namespace App\Constants;


class MallConstant
{
    // Mall 리스트
    public const MALL_EASYSELL = "easySell";
    public const MALL_LIST = [
        self::MALL_EASYSELL
    ];

    //상품등록상태 (등록 / 미등록)
    public const UNREGIST = "S";
    public const REGISTED = "Y";

    public const REGIST_SUCCESS = "Y";
    public const REGIST_FAIL    = "N";

    public const MODI_SUCCESS = "Y";
    public const MODI_FAIL    = "N";
}
