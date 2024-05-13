<?php

namespace App\Constants;


class InspectConstant
{
    /** 검수 종료 */
    public const INSPECT_IMAGE   = "image";
    public const INSPECT_PRODUCT = "product";
    public const INSPECT_NOTICE  = "notice";

    /** 검수완료 여부 */
    public const IS_INSPECT_Y = "Y";
    public const IS_INSPECT_N = "N";

    public const INSPECT_STATUS = [
        self::INSPECT_IMAGE,
        self::INSPECT_PRODUCT,
        self::INSPECT_NOTICE,
    ];

    public const INSPECT_LIST = [
        self::INSPECT_IMAGE   => "이미지",
        self::INSPECT_PRODUCT => "상품정보",
        self::INSPECT_NOTICE  => "정보고시"
    ];

}
