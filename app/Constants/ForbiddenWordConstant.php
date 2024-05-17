<?php

namespace App\Constants;


class ForbiddenWordConstant
{
    /** 키워드 유형 */
    public const KEYWORD_DELETE  = "delete";
    public const KEYWORD_REPLACE = "replace";

    public const KEYWORD_STATUS = [
        self::KEYWORD_DELETE  => "삭제",
        self::KEYWORD_REPLACE => "교체"
    ];

    /** 키워드 적용 */
    public const KEYWORD_APPLY_ALL   = "all";
    public const KEYWORD_APPLY_TITLE = "prd_name";
    public const KEYWORD_APPLY_DESC  = "prd_desc";

    public const KEYWORD_APPLY_STATUS = [
        self::KEYWORD_APPLY_ALL   => "상품명, 상세페이지",
        self::KEYWORD_APPLY_TITLE => "상품명",
        self::KEYWORD_APPLY_DESC  => "상세페이지"
    ];

    /** 정보고시 키워드 적용 */
    public const KEYWORD_APPLY_ATTR_NAME  = "attr_name";
    public const KEYWORD_APPLY_ATTR_VALUE = "attr_value";

    public const KEYWORD_APPLY_ATTR_STATUS = [
        self::KEYWORD_APPLY_ALL        => "항목명, 항목값",
        self::KEYWORD_APPLY_ATTR_NAME  => "항목명",
        self::KEYWORD_APPLY_ATTR_VALUE => "항목값"
    ];
}
