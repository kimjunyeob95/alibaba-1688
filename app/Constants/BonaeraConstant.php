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
    public const GROUP_STATUS_301   = "301";
    public const GROUP_STATUS_302   = "302";
    public const GROUP_STATUS_303   = "303";
    public const GROUP_STATUS_304   = "304";
    public const GROUP_STATUS_305   = "305";
    public const GROUP_STATUS_306   = "306";
    public const GROUP_STATUS_307   = "307";
    public const GROUP_STATUS__PEKI = "_peki";
    public const GROUP_STATUS_300   = "300";
    public const GROUP_STATUS       = [
        self::GROUP_STATUS_301   => "입고대기",
        self::GROUP_STATUS_302   => "입고완료",
        self::GROUP_STATUS_303   => "무게측정",
        self::GROUP_STATUS_304   => "결제대기",
        self::GROUP_STATUS_305   => "결제확인중",
        self::GROUP_STATUS_306   => "출고준비",
        self::GROUP_STATUS_307   => "출고완료",
        self::GROUP_STATUS__PEKI => "입고대기 or 입고완료의 폐기",
        self::GROUP_STATUS_300   => "무게측정단계의 폐기",
    ];
}
