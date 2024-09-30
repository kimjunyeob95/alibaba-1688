<?php

namespace App\Constants;


class BonaeraConstant
{
    public const USER_ID = "korea";

    /** 입고상태 */
    public const WAREHOUSE_STATUS_PENDING  = "1001";
    public const WAREHOUSE_STATUS_RECEIVED = "1003";
    public const WAREHOUSE_STATUS_DISPOSED = "1000";
    public const WAREHOUSE_STATUS          = [
        self::WAREHOUSE_STATUS_PENDING  => "입고대기",
        self::WAREHOUSE_STATUS_RECEIVED => "입고완료",
        self::WAREHOUSE_STATUS_DISPOSED => "폐기",
    ];

    /** 재고상태 */
    public const LACK_STATUS_Y = "Y";
    public const LACK_STATUS_N = "N";
    public const LACK_STATUS   = [
        self::LACK_STATUS_Y => "재고있음",
        self::LACK_STATUS_N => "재고소진",
    ];

    /** 그룹 신청서 상태 */
    public const GROUP_STATUS_301      = "301";
    public const GROUP_STATUS_302      = "302";
    public const GROUP_STATUS_303      = "303";
    public const GROUP_STATUS_304      = "304";
    public const GROUP_STATUS_305      = "305";
    public const GROUP_STATUS_306      = "306";
    public const GROUP_STATUS_307      = "307";
    public const GROUP_STATUS_301_PEKI = "301_peki";
    public const GROUP_STATUS_302_PEKI = "302_peki";
    public const GROUP_STATUS_303_PEKI = "303_peki";
    public const GROUP_STATUS_304_PEKI = "304_peki";
    public const GROUP_STATUS_305_PEKI = "305_peki";
    public const GROUP_STATUS_306_PEKI = "306_peki";
    public const GROUP_STATUS_307_PEKI = "307_peki";
    public const GROUP_STATUS_300      = "300";
    public const GROUP_STATUS          = [
        self::GROUP_STATUS_301      => "입고대기",
        self::GROUP_STATUS_302      => "입고완료",
        self::GROUP_STATUS_303      => "무게측정",
        self::GROUP_STATUS_304      => "결제대기",
        self::GROUP_STATUS_305      => "결제확인중",
        self::GROUP_STATUS_306      => "출고준비",
        self::GROUP_STATUS_307      => "출고완료",
        self::GROUP_STATUS_300      => "폐기",
        self::GROUP_STATUS_301_PEKI => self::GROUP_STATUS_301 . "에서 폐기",
        self::GROUP_STATUS_302_PEKI => self::GROUP_STATUS_302 . "에서 폐기",
        self::GROUP_STATUS_303_PEKI => self::GROUP_STATUS_303 . "에서 폐기",
        self::GROUP_STATUS_304_PEKI => self::GROUP_STATUS_304 . "에서 폐기",
        self::GROUP_STATUS_305_PEKI => self::GROUP_STATUS_305 . "에서 폐기",
        self::GROUP_STATUS_306_PEKI => self::GROUP_STATUS_306 . "에서 폐기",
        self::GROUP_STATUS_307_PEKI => self::GROUP_STATUS_307 . "에서 폐기",

    ];
}
