<?php

namespace App\Constants;


class ExchangeRateConstant
{
    /** 요청 타입 */
    public const REQUEST_TYPE_JSON = "json";
    public const REQUEST_TYPE_XML  = "xml";

    /** 언어 구분 */
    public const LANG_TYPE_KR = "kr";

    /** 통계표 코드 */
    public const STAT_CODE = "731Y001";

    /** 주기 */
    public const CYCLE_YEAR       = "A";
    public const CYCLE_HALF_YEAR  = "S";
    public const CYCLE_QUARTER    = "Q";
    public const CYCLE_MONTH      = "M";
    public const CYCLE_HALF_MONTH = "SM";
    public const CYCLE_DAY        = "D";

    /** 통계항목코드 - 통화단위 */
    public const ITEM_CODE_CNH = "0000053";

    /** 통화단위 */
    public const CURRENCY_UNIT = [
        self::ITEM_CODE_CNH => "CNH"
    ];
}
