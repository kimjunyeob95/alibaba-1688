<?php

namespace App\Constants;

class WmsConstant
{
    public const USER_ID = "korea";

    /** hscode 검색종류 */
    public const HSCODE_SEARCH_TYPE_KO     = "ko";
    public const HSCODE_SEARCH_TYPE_EN     = "en";
    public const HSCODE_SEARCH_TYPE_HSCODE = "hscode";
    public const HSCODE_SEARCH_TYPE        = [
        self::HSCODE_SEARCH_TYPE_KO     => "한글명",
        self::HSCODE_SEARCH_TYPE_EN     => "영문명",
        self::HSCODE_SEARCH_TYPE_HSCODE => "HS code",
    ];
}
