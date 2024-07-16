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
    public const MALL_NAME = [
        self::MALL_ONCHANNEL => "이지셀",
        self::MALL_EASYSELL  => "온채널",
    ];

    /** 전송 채널 리스트 */
    public const OC_PUBLIC         = "30";
    public const OC_PRIVATE        = "28";
    public const EASYSELL_W        = "W";
    public const EASYSELL_DROPHUB  = "DropHub";
    public const SEND_CHANNEL_LIST = [
        self::OC_PUBLIC        => "온채널: 일반상품",
        self::OC_PRIVATE       => "온채널: 사입상품",
        self::EASYSELL_W       => "이지셀: 더블유",
        self::EASYSELL_DROPHUB => "이지셀: Drop Hub",
    ];
    public const SEND_CHANNEL_NAME_LIST = [
        self::OC_PUBLIC        => self::MALL_ONCHANNEL,
        self::OC_PRIVATE       => self::MALL_ONCHANNEL,
        self::EASYSELL_W       => self::MALL_EASYSELL,
        self::EASYSELL_DROPHUB => self::MALL_EASYSELL,
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

    /** 자동등록 타입 */
    public const AUTO_REGIST_TRUE  = "true";
    public const AUTO_REGIST_FALSE = "false";

    /** 등록여부 */
    public const REGIST_Y = "Y";
    public const REGIST_N = "N";
}
