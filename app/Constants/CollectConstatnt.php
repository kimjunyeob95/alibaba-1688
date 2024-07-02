<?php

namespace App\Constants;


class CollectConstatnt
{
    /** 자동번역 여부 */
    public const AI_ACTIVE_TRUE  = "true";
    public const AI_ACTIVE_FALSE = "false";

    /** 수집 관련 */
    public const COLLECT_NONE       = "none";
    public const COLLECT_PRODUCT    = "product";
    public const TRANSLATE_NONE     = "none";
    public const TRANSLATE_ALL      = "all";
    public const TRANSLATE_STATUS_Y = "statusY";
    public const TRANSLATE_STATUS_N = "statusN";
    public const COLLECT_NAME_KR    = [
        self::COLLECT_NONE       => "선택 없음",
        self::COLLECT_PRODUCT    => "상품 수집",
        self::TRANSLATE_NONE     => "선택 없음",
        self::TRANSLATE_ALL      => "전체 상품",
        self::TRANSLATE_STATUS_Y => "번역완료 상품",
        self::TRANSLATE_STATUS_N => "미 번역 상품",
    ];
}
