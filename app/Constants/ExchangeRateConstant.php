<?php

namespace App\Constants;


class ExchangeRateConstant
{
    /** 검색요청 타입 */
    public const AP01 = "AP01";
    public const AP02 = "AP02";
    public const AP03 = "AP03";
    public const TYPE_LIST = [
        self::AP01 => "환율",
        self::AP02 => "대출금리",
        self::AP03 => "국제금리",
    ];

    /** 통화단위 */
    public const CURRENCY_UNIT = "CNH";
}
