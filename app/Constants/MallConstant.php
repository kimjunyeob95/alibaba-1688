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

    /** 전송 채널 리스트 */
    public const OC_PUBLIC         = "30";
    public const OC_PRIVATE        = "28";
    public const SEND_CHANNEL_LIST = [
        self::OC_PUBLIC  => "온채널 일반 상품",
        self::OC_PRIVATE => "온채널 사입 상품",
    ];

    /** 상품등록상태 (등록 / 미등록) */
    public const UNREGIST = "S";
    public const REGISTED = "Y";
    
    /** 성공여부 */
    public const REGIST_SUCCESS = "Y";
    public const REGIST_FAIL    = "N";
    public const REGIST_ERROR   = "E";

    public const REGIST_TYPE_LIST = [
        self::REGIST_SUCCESS => "성공",
        self::REGIST_FAIL    => "실패",
        self::REGIST_ERROR   => "에러",
    ];

    public const MODI_SUCCESS = "Y";
    public const MODI_FAIL    = "N";

    /** 전송 타입 */
    public const SEND_TYPE_REGIST = "regist";
    public const SEND_TYPE_MODI   = "modi";
    public const SEND_TYPE_LIST = [
        self::SEND_TYPE_REGIST => "등록",
        self::SEND_TYPE_MODI   => "수정",
    ];


}
