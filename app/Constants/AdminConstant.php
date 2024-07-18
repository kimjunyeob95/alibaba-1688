<?php

namespace App\Constants;


class AdminConstant
{
    /** 사업부 리스트 */
    public const OC      = "onchannel";
    public const ES      = "easySell";
    public const SIXSHOP = "sixshop";
    public const GENUIO  = "genuio";
    public const COMPANY = [
        self::OC      => "온채널",
        self::ES      => "이지셀",
        self::SIXSHOP => "식스샵",
        self::GENUIO  => "제누이오",
    ];

    /** 권한(level) 리스트 */
    public const SUPER      = "super";
    public const PUBLIC     = "public";
    public const LEVEL_NAME = [
        self::SUPER  => "최고관리자",
        self::PUBLIC => "일반관리자",
    ];

     /** 권한(level) 별 제외할 네비게이션 메뉴 */
     public const EXCLUDED_NAV = [
        self::SUPER  => [],
        self::PUBLIC => [
            NavConstant::ADMIN_MANAGE
        ],
    ];
}
